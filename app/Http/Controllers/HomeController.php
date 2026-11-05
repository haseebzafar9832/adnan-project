<?php

namespace App\Http\Controllers;

use App\Models\BulkGroup;
use App\Models\Category;
use App\Models\DiscountTier;
use App\Models\GroupMember;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::query()->active()->withCount(['products' => fn ($query) => $query->active()])->orderBy('name')->get();
        $groups = BulkGroup::query()
            ->with(['product.category', 'creator'])
            ->withCount('members')
            ->whereIn('status', ['open', 'reached'])
            ->latest()
            ->take(6)
            ->get();

        $featured = $groups->sortByDesc(fn (BulkGroup $group) => $group->progressPercent())->first();
        $products = Product::query()->active()->with('category')->orderBy('name')->take(6)->get();
        $tiers = DiscountTier::query()->whereNull('product_id')->orderBy('min_quantity')->get();

        $stats = [
            'buyers' => User::query()->where('role', 'user')->where('is_active', true)->count(),
            'groups' => BulkGroup::query()->count(),
            'saved' => $this->savings(),
        ];

        return view('home', compact('categories', 'groups', 'featured', 'products', 'tiers', 'stats'));
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $memberships = GroupMember::query()
            ->with(['group.product', 'orders.payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $orders = Order::query()->where('user_id', $user->id)->latest()->take(5)->get();
        $alerts = $user->alerts()->latest()->take(5)->get();
        $groupCount = $user->memberships()->count();
        $orderCount = Order::query()->where('user_id', $user->id)->count();

        $saved = 0;
        $pending = 0;

        foreach ($user->memberships()->with('group.product')->get() as $membership) {
            $base = (float) $membership->group->product->base_price;
            $unit = $membership->locked_unit_price !== null
                ? (float) $membership->locked_unit_price
                : $membership->group->liveQuote()['unit_price'];

            if ($membership->payment_status === 'paid') {
                $saved += max(0, ($base - $unit) * $membership->quantity);
            }

            if ($membership->payment_status === 'pending') {
                $pending++;
            }
        }

        return view('dashboard', compact('memberships', 'orders', 'alerts', 'saved', 'pending', 'groupCount', 'orderCount'));
    }

    private function savings(): float
    {
        return (float) GroupMember::query()
            ->where('payment_status', 'paid')
            ->whereNotNull('locked_unit_price')
            ->with('group.product')
            ->get()
            ->sum(function (GroupMember $member) {
                $base = (float) $member->group->product->base_price;

                return max(0, ($base - (float) $member->locked_unit_price) * $member->quantity);
            });
    }
}
