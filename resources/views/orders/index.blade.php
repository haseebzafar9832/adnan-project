@extends('layouts.app')
@section('title', 'Orders · BulkShare')

@section('content')
    <x-page-head eyebrow="Delivery" title="My orders" lede="Each payment creates its own order, even when the goods were bought inside a shared group." />
    <div class="table-card">
        <table class="table">
            <thead>
                <tr><th>Order</th><th>Group</th><th>Total</th><th>Payment</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('orders.show', $order) }}">{{ $order->number }}</a></td>
                        <td>{{ $order->group->title }}</td>
                        <td>@pkr($order->total)</td>
                        <td>{{ $order->payment?->statusLabel() }}</td>
                        <td><x-status :status="$order->status" :label="$order->statusLabel()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
