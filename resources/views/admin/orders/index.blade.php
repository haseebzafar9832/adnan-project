@extends('layouts.admin')
@section('title', 'Orders · Admin')

@section('content')
    <h1 class="serif">Orders</h1>
    <div class="filters">
        <a class="{{ request('status') ? '' : 'is-on' }}" href="{{ route('admin.orders.index') }}">All</a>
        @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $key)
            <a class="{{ request('status') === $key ? 'is-on' : '' }}" href="{{ route('admin.orders.index', ['status' => $key]) }}">{{ ucfirst($key) }}</a>
        @endforeach
    </div>
    <div class="table-card">
        <table class="table">
            <thead><tr><th>Order</th><th>Buyer</th><th>Total</th><th>Update</th></tr></thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('orders.show', $order) }}">{{ $order->number }}</a>
                            <div class="muted">{{ $order->group->title }}</div>
                        </td>
                        <td>{{ $order->user->name }}</td>
                        <td>@pkr($order->total)</td>
                        <td>
                            @if($order->status !== 'cancelled')
                                <form class="d-flex flex-wrap gap-2" method="POST" action="{{ route('admin.orders.update', $order) }}">
                                    @csrf
                                    @method('PUT')
                                    <select class="form-select" style="max-width: 160px" name="status">
                                        @foreach(['confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'] as $key => $label)
                                            <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control" style="max-width: 260px" name="tracking_note" value="{{ $order->tracking_note }}" placeholder="Tracking note">
                                    <button class="btn btn-brand btn-sm" type="submit">Save</button>
                                </form>
                            @else
                                <x-status status="cancelled" label="Cancelled" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
