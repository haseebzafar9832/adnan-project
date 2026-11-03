@extends('layouts.admin')
@section('title', 'Transactions · Admin')

@section('content')
    <h1 class="serif">Transactions</h1>
    <div class="filters">
        <a class="{{ request('status') ? '' : 'is-on' }}" href="{{ route('admin.transactions.index') }}">All</a>
        @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'refunded' => 'Refunded', 'failed' => 'Failed'] as $key => $label)
            <a class="{{ request('status') === $key ? 'is-on' : '' }}" href="{{ route('admin.transactions.index', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="table-card">
        <table class="table">
            <thead><tr><th>Reference</th><th>Buyer</th><th>Method</th><th>Amount</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->reference }}<div class="muted">{{ $payment->order->number }}</div></td>
                        <td>{{ $payment->user->name }}<div class="muted">{{ $payment->payer_hint }}</div></td>
                        <td>{{ $payment->methodLabel() }}</td>
                        <td>@pkr($payment->amount)</td>
                        <td><x-status :status="$payment->status" :label="$payment->statusLabel()" /></td>
                        <td class="text-end">
                            @if($payment->status === 'pending')
                                <form class="d-inline" method="POST" action="{{ route('admin.transactions.update', $payment) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="confirm">
                                    <button class="btn btn-brand btn-sm" type="submit">Confirm</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('admin.transactions.update', $payment) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-line btn-sm" type="submit">Reject</button>
                                </form>
                            @elseif($payment->status === 'paid')
                                <form class="d-inline" method="POST" action="{{ route('admin.transactions.update', $payment) }}" onsubmit="return confirm('Refund this payment?');">
                                    @csrf
                                    <input type="hidden" name="action" value="refund">
                                    <button class="btn btn-link text-danger" type="submit">Refund</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $payments->links() }}</div>
@endsection
