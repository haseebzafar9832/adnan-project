@extends('layouts.app')
@section('title', $order->number.' · BulkShare')

@section('content')
    @php
        $steps = ['confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
        $keys = array_keys($steps);
        $current = array_search($order->status, $keys, true);
    @endphp
    <x-page-head eyebrow="Order" title="{{ $order->number }}" lede="{{ $order->group->product->name }} from {{ $order->group->title }}." />
    <div class="split">
        <section class="panel">
            @if($order->status === 'cancelled')
                <div class="alert alert-danger border-0">This order was cancelled. {{ $order->tracking_note }}</div>
            @elseif($order->status === 'pending')
                <div class="alert alert-warning border-0">Payment is still pending confirmation.</div>
            @else
                <div class="timeline">
                    @foreach($steps as $key => $label)
                        @php($index = array_search($key, $keys, true))
                        <div class="{{ $current !== false && $index <= $current ? 'is-done' : '' }} {{ $key === $order->status ? 'is-now' : '' }}">{{ $label }}</div>
                    @endforeach
                </div>
            @endif
            @if($order->tracking_note)
                <p><strong>Update.</strong> {{ $order->tracking_note }}</p>
            @endif
            <div class="member"><span>Quantity</span><strong>{{ $order->quantity }} {{ $order->group->product->unit }}</strong></div>
            <div class="member"><span>Unit price</span><strong>@pkr($order->unit_price)</strong></div>
            <div class="member"><span>Discount</span><strong>@pct($order->discount_percent)</strong></div>
            <div class="member"><span>Total</span><strong>@pkr($order->total)</strong></div>
            <div class="member"><span>Deliver to</span><span>{{ $order->shipping_address }}{{ $order->city ? ', '.$order->city : '' }}</span></div>
        </section>
        <aside class="panel">
            <h2>Payment</h2>
            @if($order->payment)
                <p><x-status :status="$order->payment->status" :label="$order->payment->statusLabel()" /></p>
                <p>{{ $order->payment->methodLabel() }}</p>
                <p>Reference <strong>{{ $order->payment->reference }}</strong></p>
                @if($order->payment->payer_hint)<p class="muted">{{ $order->payment->payer_hint }}</p>@endif
                <p class="muted mb-0">Paid by {{ $order->user->name }}. Other members have their own payments.</p>
            @else
                <p class="muted">No payment record.</p>
            @endif
        </aside>
    </div>
@endsection
