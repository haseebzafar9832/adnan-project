<?php

namespace App\Http\Controllers;

use App\Models\BulkGroup;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Product;
use App\Services\GroupService;
use App\Services\PricingService;
use Illuminate\Http\Request;

class BulkGroupController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status', 'active');
        $groups = BulkGroup::query()
            ->with(['product.category', 'creator'])
            ->withCount('members')
            ->when($status === 'active', fn ($query) => $query->whereIn('status', ['open', 'reached']))
            ->when(in_array($status, ['open', 'reached', 'closed', 'fulfilled', 'cancelled'], true), fn ($query) => $query->where('status', $status))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                        ->orWhereHas('product', fn ($product) => $product->where('name', 'like', $term));
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('groups.index', compact('groups', 'status'));
    }

    public function show(Request $request, BulkGroup $group)
    {
        $group->load(['product.category', 'creator', 'members.user']);
        $membership = $request->user()
            ? $group->members->firstWhere('user_id', $request->user()->id)
            : null;

        $messages = $membership
            ? $group->messages()->with('user')->latest('id')->take(40)->get()->sortBy('id')
            : collect();

        return view('groups.show', [
            'group' => $group,
            'messages' => $messages,
            'membership' => $membership,
            'tiers' => app(PricingService::class)->tiersFor($group->product),
            'joinQuote' => app(PricingService::class)->quote($group->product, $group->current_quantity + 1, 1),
        ]);
    }

    public function create(Request $request, PricingService $pricing)
    {
        $products = Product::query()->with('category')->active()->orderBy('name')->get();
        $selected = $products->firstWhere('slug', $request->query('product'));
        $productId = (int) old('product_id', $selected?->id);
        $product = $products->firstWhere('id', $productId);
        $mine = max(1, (int) old('my_quantity', 1));
        $target = max($mine + 1, (int) old('target_quantity', 20));
        $preview = null;

        if ($product) {
            $preview = [
                'solo' => $pricing->quote($product, $mine, $mine),
                'target' => $pricing->quote($product, $target, $mine),
            ];
        }

        return view('groups.create', compact('products', 'productId', 'preview', 'mine', 'target'));
    }

    public function store(Request $request, GroupService $groups)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'my_quantity' => ['required', 'integer', 'min:1', 'max:500'],
            'target_quantity' => ['required', 'integer', 'max:5000', 'gt:my_quantity'],
            'deadline' => ['required', 'date', 'after:today', 'before:+120 days'],
        ]);

        $group = $groups->create($request->user(), $data);

        return redirect()->route('groups.show', $group)->with('success', 'Your bulk group is open. Share it so the price can drop.');
    }

    public function join(Request $request, BulkGroup $group, GroupService $groups)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $groups->join($request->user(), $group, (int) $data['quantity']);
        $group->refresh();
        $message = $group->status === 'reached'
            ? 'You joined, and the group has now reached its target.'
            : 'You joined the group. Your quantity counts toward the discount.';

        return redirect()->route('groups.show', $group)->with('success', $message);
    }

    public function close(Request $request, BulkGroup $group, GroupService $groups)
    {
        abort_unless($group->user_id === $request->user()->id, 403);
        $groups->close($group, $request->user());

        return back()->with('success', 'The group is closed. Members can still pay their share.');
    }

    public function mine(Request $request)
    {
        $memberships = GroupMember::query()
            ->with(['group.product.category', 'group.creator', 'orders'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(8);

        return view('groups.mine', compact('memberships'));
    }
}
