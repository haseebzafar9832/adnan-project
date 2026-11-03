@extends('layouts.app')
@section('title', 'My groups · BulkShare')

@section('content')
    <x-page-head eyebrow="Memberships" title="My groups" lede="Every pool you created or joined, with the payment state of your share." />
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Group</th>
                    <th>Quantity</th>
                    <th>Unit price</th>
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
                            <div class="muted">{{ $membership->group->product->name }}</div>
                        </td>
                        <td>{{ $membership->quantity }} {{ $membership->group->product->unit }}</td>
                        <td>@pkr($unit)</td>
                        <td><x-status :status="$membership->payment_status" :label="$membership->paymentLabel()" /></td>
                        <td class="text-end">
                            @if($membership->payment_status !== 'paid')
                                <a class="btn btn-brand btn-sm" href="{{ route('payments.show', $membership->group) }}">Pay</a>
                            @elseif($order = $membership->orders->first())
                                <a href="{{ route('orders.show', $order) }}">Order</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">You have not joined a group yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $memberships->links() }}</div>
@endsection
