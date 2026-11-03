@extends('layouts.admin')
@section('title', 'Admin · BulkShare')

@section('content')
    <h1 class="serif">Operations</h1>
    <div class="stat-grid">
        <article class="stat-card"><span>Buyers</span><strong>{{ $stats['users'] }}</strong></article>
        <article class="stat-card"><span>Open groups</span><strong>{{ $stats['open_groups'] }}</strong></article>
        <article class="stat-card"><span>Paid volume</span><strong>@pkr($stats['revenue'])</strong></article>
        <article class="stat-card"><span>Pending transfers</span><strong>{{ $stats['pending'] }}</strong></article>
    </div>
    <div class="dash-grid">
        <section class="table-card">
            <table class="table">
                <thead><tr><th>Recent orders</th><th>Buyer</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->number }}<div class="muted">{{ $order->group->product->name }}</div></td>
                            <td>{{ $order->user->name }}</td>
                            <td><x-status :status="$order->status" :label="$order->statusLabel()" /></td>
                            <td>@pkr($order->total)</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        <section class="panel">
            <h2>Closing soon</h2>
            @foreach($groups as $group)
                <div class="member">
                    <div>
                        <strong>{{ $group->title }}</strong>
                        <div class="muted">{{ $group->current_quantity }}/{{ $group->target_quantity }} · {{ $group->deadline->diffForHumans() }}</div>
                    </div>
                    <x-status :status="$group->status" :label="$group->statusLabel()" />
                </div>
            @endforeach
            <h2 class="mt-4">Latest payments</h2>
            @foreach($payments as $payment)
                <div class="member">
                    <div>
                        <strong>{{ $payment->user->name }}</strong>
                        <div class="muted">{{ $payment->reference }}</div>
                    </div>
                    <x-status :status="$payment->status" :label="$payment->statusLabel()" />
                </div>
            @endforeach
        </section>
    </div>
@endsection
