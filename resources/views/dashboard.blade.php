@extends('layouts.app')
@section('title', 'Dashboard · BulkShare')

@section('content')
    <h1 class="serif">Dashboard</h1>
    <div class="stat-grid">
        <article class="stat-card"><span>Groups</span><strong>{{ $groupCount }}</strong></article>
        <article class="stat-card"><span>Waiting for payment</span><strong>{{ $pending }}</strong></article>
        <article class="stat-card"><span>Saved on paid shares</span><strong>@pkr($saved)</strong></article>
        <article class="stat-card"><span>Orders</span><strong>{{ $orderCount }}</strong></article>
    </div>
    <div class="dash-grid">
        <section class="table-card">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Recent groups</th>
                        <th>Share</th>
                        <th>Payment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberships as $membership)
                        @php($quote = $membership->group->liveQuote())
                        @php($unit = $membership->locked_unit_price ?? $quote['unit_price'])
                        <tr>
                            <td>
                                <a href="{{ route('groups.show', $membership->group) }}"><strong>{{ $membership->group->title }}</strong></a>
                                <div class="muted">{{ $membership->group->current_quantity }}/{{ $membership->group->target_quantity }} {{ $membership->group->product->unit }}</div>
                            </td>
                            <td>{{ $membership->quantity }} {{ $membership->group->product->unit }}<div class="muted">@pkr($unit)</div></td>
                            <td><x-status :status="$membership->payment_status" :label="$membership->paymentLabel()" /></td>
                            <td class="text-end">
                                @if($membership->payment_status !== 'paid')
                                    <a class="btn btn-line btn-sm" href="{{ route('payments.show', $membership->group) }}">Pay</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">You have not joined a group yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
        <section class="panel">
            <h2>Notices</h2>
            @forelse($alerts as $alert)
                <a class="d-block py-2 text-decoration-none border-bottom" href="{{ route('notifications.open', $alert) }}">
                    <strong>{{ $alert->title }}</strong>
                    <div class="muted">{{ $alert->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <p class="muted">No notifications yet.</p>
            @endforelse
            <h2 class="mt-4">Orders</h2>
            @forelse($orders as $order)
                <div class="member">
                    <a href="{{ route('orders.show', $order) }}">{{ $order->number }}</a>
                    <x-status :status="$order->status" :label="$order->statusLabel()" />
                </div>
            @empty
                <p class="muted mb-0">No orders yet.</p>
            @endforelse
        </section>
    </div>
@endsection
