<?php

namespace App\Services;

use App\Models\BulkGroup;
use App\Models\GroupMember;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GroupService
{
    public function __construct(
        private PricingService $pricing,
        private Notifier $notifier,
    ) {}

    public function create(User $user, array $data): BulkGroup
    {
        return DB::transaction(function () use ($user, $data) {
            $product = Product::query()->findOrFail($data['product_id']);

            if (! $product->is_active) {
                throw ValidationException::withMessages([
                    'product_id' => 'This product is not open for a new group.',
                ]);
            }

            $mine = (int) $data['my_quantity'];
            $target = (int) $data['target_quantity'];

            $group = BulkGroup::query()->create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'target_quantity' => $target,
                'current_quantity' => $mine,
                'deadline' => $data['deadline'],
                'status' => $mine >= $target ? 'reached' : 'open',
            ]);

            GroupMember::query()->create([
                'bulk_group_id' => $group->id,
                'user_id' => $user->id,
                'quantity' => $mine,
                'payment_status' => 'pending',
            ]);

            DB::afterCommit(function () use ($user, $group) {
                $this->notifier->to(
                    $user,
                    'Bulk group is live',
                    'You created "'.$group->title.'". Invite others so the quantity can unlock a lower price.',
                    'group',
                    route('groups.show', $group, false),
                );
            });

            return $group;
        });
    }

    public function join(User $user, BulkGroup $group, int $quantity): GroupMember
    {
        return DB::transaction(function () use ($user, $group, $quantity) {
            $locked = BulkGroup::query()->whereKey($group->id)->lockForUpdate()->firstOrFail();

            if (! $locked->isJoinable()) {
                throw ValidationException::withMessages([
                    'quantity' => 'This group is no longer accepting members.',
                ]);
            }

            $already = GroupMember::query()
                ->where('bulk_group_id', $locked->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($already) {
                throw ValidationException::withMessages([
                    'quantity' => 'You already joined this group.',
                ]);
            }

            $member = GroupMember::query()->create([
                'bulk_group_id' => $locked->id,
                'user_id' => $user->id,
                'quantity' => $quantity,
                'payment_status' => 'pending',
            ]);

            $locked->current_quantity += $quantity;
            $reached = false;

            if ($locked->status === 'open' && $locked->current_quantity >= $locked->target_quantity) {
                $locked->status = 'reached';
                $reached = true;
            }

            $locked->save();

            DB::afterCommit(function () use ($locked, $user, $quantity, $reached) {
                $locked->load(['product', 'creator', 'members.user']);

                if ($reached) {
                    foreach ($locked->members as $person) {
                        $this->notifier->to(
                            $person->user,
                            'Target quantity reached',
                            '"'.$locked->title.'" hit '.$locked->target_quantity.' '.$locked->product->unit.'. Pay your share to lock the discount.',
                            'target',
                            route('groups.show', $locked, false),
                        );
                    }

                    return;
                }

                if ($locked->creator && $locked->creator->id !== $user->id) {
                    $this->notifier->to(
                        $locked->creator,
                        'A neighbour joined your group',
                        $user->name.' pledged '.$quantity.' '.$locked->product->unit.' in "'.$locked->title.'".',
                        'group',
                        route('groups.show', $locked, false),
                    );
                }
            });

            return $member;
        });
    }

    public function settlePayment(User $user, GroupMember $member, array $data): Order
    {
        return DB::transaction(function () use ($user, $member, $data) {
            $member = GroupMember::query()->whereKey($member->id)->lockForUpdate()->firstOrFail();
            $group = BulkGroup::query()->whereKey($member->bulk_group_id)->lockForUpdate()->firstOrFail();
            $group->load('product');

            if ($member->user_id !== $user->id) {
                abort(403);
            }

            if ($group->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'method' => 'This group was cancelled.',
                ]);
            }

            if ($member->payment_status === 'paid') {
                throw ValidationException::withMessages([
                    'method' => 'This share is already paid.',
                ]);
            }

            $openOrder = Order::query()
                ->where('group_member_id', $member->id)
                ->whereNotIn('status', ['cancelled'])
                ->exists();

            if ($openOrder) {
                throw ValidationException::withMessages([
                    'method' => 'A payment for this share is already in progress.',
                ]);
            }

            $quote = $this->pricing->quote($group->product, (int) $group->current_quantity, (int) $member->quantity);
            $instant = $data['method'] !== 'bank';

            $order = Order::query()->create([
                'number' => 'TMP-'.Str::ulid(),
                'user_id' => $user->id,
                'bulk_group_id' => $group->id,
                'group_member_id' => $member->id,
                'quantity' => $member->quantity,
                'unit_price' => $quote['unit_price'],
                'discount_percent' => $quote['discount_percent'],
                'total' => $quote['line_total'],
                'status' => $instant ? 'confirmed' : 'pending',
                'shipping_address' => $data['shipping_address'],
                'city' => $data['city'] ?? null,
            ]);

            $order->number = 'BS-'.now()->format('Y').'-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
            $order->save();

            Payment::query()->create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $quote['line_total'],
                'method' => $data['method'],
                'reference' => $this->reference(),
                'payer_hint' => $data['payer_hint'] ?? null,
                'status' => $instant ? 'paid' : 'pending',
                'paid_at' => $instant ? now() : null,
            ]);

            $member->locked_unit_price = $quote['unit_price'];
            $member->discount_percent = $quote['discount_percent'];

            if ($instant) {
                $member->payment_status = 'paid';
            }

            $member->save();

            if ($instant) {
                $this->maybeFulfill($group);
            }

            DB::afterCommit(function () use ($user, $order, $instant) {
                $this->notifier->to(
                    $user,
                    $instant ? 'Payment received' : 'Transfer submitted',
                    $instant
                        ? 'Order '.$order->number.' is confirmed. Track it from your orders.'
                        : 'Order '.$order->number.' is waiting for an admin to confirm the bank transfer.',
                    'payment',
                    route('orders.show', $order, false),
                );
            });

            return $order->load('payment');
        });
    }

    public function confirmPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'pending') {
                throw ValidationException::withMessages([
                    'action' => 'Only a pending payment can be confirmed.',
                ]);
            }

            $payment->status = 'paid';
            $payment->paid_at = now();
            $payment->save();

            $order = $payment->order()->lockForUpdate()->firstOrFail();
            $order->status = 'confirmed';
            $order->save();

            $member = $order->member()->lockForUpdate()->firstOrFail();
            $member->payment_status = 'paid';
            $member->save();

            $group = $order->group()->lockForUpdate()->firstOrFail();
            $this->maybeFulfill($group);

            DB::afterCommit(function () use ($payment, $order) {
                $this->notifier->to(
                    $payment->user,
                    'Payment confirmed',
                    'The transfer for '.$order->number.' is confirmed.',
                    'payment',
                    route('orders.show', $order, false),
                );
            });
        });
    }

    public function rejectPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'pending') {
                throw ValidationException::withMessages([
                    'action' => 'Only a pending payment can be rejected.',
                ]);
            }

            $order = $payment->order()->firstOrFail();
            $member = $order->member()->lockForUpdate()->firstOrFail();

            $payment->status = 'failed';
            $payment->save();

            $order->status = 'cancelled';
            $order->tracking_note = 'The bank transfer was not confirmed. You can submit payment again.';
            $order->save();

            $member->payment_status = 'pending';
            $member->locked_unit_price = null;
            $member->discount_percent = null;
            $member->save();

            DB::afterCommit(function () use ($payment, $order) {
                $this->notifier->to(
                    $payment->user,
                    'Transfer not confirmed',
                    'We could not confirm the transfer for '.$order->number.'. Please try again.',
                    'payment',
                    route('orders.show', $order, false),
                );
            });
        });
    }

    public function refund(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'paid') {
                throw ValidationException::withMessages([
                    'action' => 'Only a paid transaction can be refunded.',
                ]);
            }

            $payment->status = 'refunded';
            $payment->save();

            $order = $payment->order()->firstOrFail();
            $order->status = 'cancelled';
            $order->tracking_note = 'Payment refunded.';
            $order->save();

            $member = $order->member()->lockForUpdate()->firstOrFail();
            $member->payment_status = 'refunded';
            $member->save();

            $group = $order->group()->lockForUpdate()->firstOrFail();

            if ($group->status === 'fulfilled') {
                $group->status = $group->deadline->isPast()
                    ? 'closed'
                    : ($group->current_quantity >= $group->target_quantity ? 'reached' : 'open');
                $group->save();
            }

            DB::afterCommit(function () use ($payment, $order) {
                $this->notifier->to(
                    $payment->user,
                    'Payment refunded',
                    'The payment for '.$order->number.' was refunded.',
                    'payment',
                    route('orders.show', $order, false),
                );
            });
        });
    }

    public function close(BulkGroup $group, ?User $actor = null): void
    {
        if (! in_array($group->status, ['open', 'reached'], true)) {
            throw ValidationException::withMessages([
                'action' => 'This group is already '.$group->statusLabel().'.',
            ]);
        }

        $group->status = 'closed';
        $group->save();
        $group->load('members.user', 'product');

        foreach ($group->members as $member) {
            $this->notifier->to(
                $member->user,
                'Group closed',
                '"'.$group->title.'" is closed to new members. Pay any remaining share to keep your place.',
                'deadline',
                route('groups.show', $group, false),
            );
        }
    }

    public function cancel(BulkGroup $group): void
    {
        if (in_array($group->status, ['fulfilled', 'cancelled'], true)) {
            throw ValidationException::withMessages([
                'action' => 'This group cannot be cancelled.',
            ]);
        }

        $paid = $group->members()->where('payment_status', 'paid')->count();

        if ($paid > 0) {
            throw ValidationException::withMessages([
                'action' => 'Refund every paid member before cancelling this group.',
            ]);
        }

        $group->status = 'cancelled';
        $group->save();

        Order::query()
            ->where('bulk_group_id', $group->id)
            ->whereNotIn('status', ['cancelled'])
            ->update([
                'status' => 'cancelled',
                'tracking_note' => 'The group was cancelled.',
            ]);

        $group->load('members.user');

        foreach ($group->members as $member) {
            $this->notifier->to(
                $member->user,
                'Group cancelled',
                '"'.$group->title.'" was cancelled by an administrator.',
                'group',
                route('groups.show', $group, false),
            );
        }
    }

    public function closeExpired(): void
    {
        $groups = BulkGroup::query()
            ->whereIn('status', ['open', 'reached'])
            ->where('deadline', '<', now())
            ->get();

        foreach ($groups as $group) {
            $this->close($group);
        }
    }

    private function maybeFulfill(BulkGroup $group): void
    {
        $unpaid = $group->members()->where('payment_status', '!=', 'paid')->count();

        if ($unpaid > 0 || $group->current_quantity < $group->target_quantity || $group->status === 'fulfilled') {
            return;
        }

        $group->status = 'fulfilled';
        $group->save();

        DB::afterCommit(function () use ($group) {
            $group->load('members.user');

            foreach ($group->members as $member) {
                $this->notifier->to(
                    $member->user,
                    'Everyone has paid',
                    '"'.$group->title.'" is fully paid. Delivery updates will appear on your order.',
                    'group',
                    route('groups.show', $group, false),
                );
            }
        });
    }

    private function reference(): string
    {
        return 'TXN-'.now()->format('ymdHis').'-'.strtoupper(Str::random(4));
    }
}
