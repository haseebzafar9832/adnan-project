<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Notifier;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['user', 'group.product', 'payment'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function update(Request $request, Order $order, Notifier $notifier)
    {
        $data = $request->validate([
            'status' => ['required', 'in:confirmed,processing,shipped,delivered'],
            'tracking_note' => ['nullable', 'string', 'max:500'],
        ]);

        if (in_array($order->status, ['cancelled'], true)) {
            return back()->with('error', 'A cancelled order cannot be moved forward.');
        }

        $order->update($data);
        $notifier->to(
            $order->user,
            'Order '.$order->number.' updated',
            'Status is now '.$order->statusLabel().'.',
            'order',
            route('orders.show', $order, false),
        );

        return back()->with('success', 'Order updated.');
    }
}
