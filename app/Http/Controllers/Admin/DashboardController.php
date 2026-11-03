<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkGroup;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::query()->where('role', 'user')->count(),
            'products' => Product::query()->count(),
            'open_groups' => BulkGroup::query()->whereIn('status', ['open', 'reached'])->count(),
            'revenue' => (float) Payment::query()->where('status', 'paid')->sum('amount'),
            'pending' => Payment::query()->where('status', 'pending')->count(),
            'orders' => Order::query()->count(),
        ];

        $orders = Order::query()->with(['user', 'group.product'])->latest()->take(6)->get();
        $payments = Payment::query()->with(['user', 'order'])->latest()->take(6)->get();
        $groups = BulkGroup::query()->with('product')->whereIn('status', ['open', 'reached'])->orderBy('deadline')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'orders', 'payments', 'groups'));
    }
}
