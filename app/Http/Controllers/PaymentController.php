<?php

namespace App\Http\Controllers;

use App\Models\BulkGroup;
use App\Models\GroupMember;
use App\Models\Order;
use App\Services\GroupService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function show(Request $request, BulkGroup $group, PricingService $pricing)
    {
        $group->load('product');
        $member = $this->member($request, $group);
        $order = $this->openOrder($member);

        if ($member->payment_status === 'paid') {
            $paidOrder = $order ?: $member->orders()->first();

            return $paidOrder
                ? redirect()->route('orders.show', $paidOrder)->with('success', 'This share is already paid.')
                : redirect()->route('orders.index')->with('success', 'This share is already paid.');
        }

        $quote = $pricing->quote($group->product, (int) $group->current_quantity, (int) $member->quantity);

        return view('payments.checkout', compact('group', 'member', 'quote', 'order'));
    }

    public function store(Request $request, BulkGroup $group, GroupService $groups)
    {
        $member = $this->member($request, $group);
        $data = $request->validate([
            'method' => ['required', 'in:card,jazzcash,easypaisa,bank'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:80'],
            'card_number' => ['nullable', 'string', 'max:23'],
            'expiry' => ['nullable', 'string', 'max:5'],
            'cvv' => ['nullable', 'string', 'max:4'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'bank_reference' => ['nullable', 'string', 'max:40'],
        ]);

        $data['payer_hint'] = $this->payerHint($request, $data['method']);
        $order = $groups->settlePayment($request->user(), $member, $data);

        $message = $data['method'] === 'bank'
            ? 'Transfer recorded. An admin will confirm it before the order moves ahead.'
            : 'Payment received. Your order is confirmed.';

        return redirect()->route('orders.show', $order)->with('success', $message);
    }

    private function member(Request $request, BulkGroup $group): GroupMember
    {
        $member = GroupMember::query()
            ->where('bulk_group_id', $group->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $member) {
            abort(redirect()->route('groups.show', $group)->with('error', 'Join the group before paying.'));
        }

        return $member;
    }

    private function openOrder(GroupMember $member): ?Order
    {
        return Order::query()
            ->where('group_member_id', $member->id)
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->first();
    }

    private function payerHint(Request $request, string $method): ?string
    {
        if ($method === 'card') {
            $digits = preg_replace('/\D/', '', (string) $request->input('card_number'));

            if (strlen($digits) !== 16) {
                throw ValidationException::withMessages(['card_number' => 'Enter a 16-digit card number.']);
            }

            if (! preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', (string) $request->input('expiry'))) {
                throw ValidationException::withMessages(['expiry' => 'Use the expiry format MM/YY.']);
            }

            [$month, $year] = explode('/', (string) $request->input('expiry'));
            $expires = now()->setDate(2000 + (int) $year, (int) $month, 1)->endOfMonth();

            if ($expires->isPast()) {
                throw ValidationException::withMessages(['expiry' => 'This card has expired.']);
            }

            if (! preg_match('/^\d{3,4}$/', (string) $request->input('cvv'))) {
                throw ValidationException::withMessages(['cvv' => 'Enter the 3 or 4 digit security code.']);
            }

            return 'Card •••• '.substr($digits, -4);
        }

        if (in_array($method, ['jazzcash', 'easypaisa'], true)) {
            $mobile = preg_replace('/\D/', '', (string) $request->input('mobile'));

            if (! preg_match('/^03\d{9}$/', $mobile)) {
                throw ValidationException::withMessages(['mobile' => 'Enter a mobile number like 03XXXXXXXXX.']);
            }

            return $mobile;
        }

        $reference = trim((string) $request->input('bank_reference'));

        if ($reference === '') {
            throw ValidationException::withMessages(['bank_reference' => 'Enter the transfer reference from your bank.']);
        }

        return $reference;
    }
}
