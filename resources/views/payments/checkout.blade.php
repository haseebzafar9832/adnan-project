@extends('layouts.app')
@section('title', 'Pay your share · BulkShare')

@section('content')
    <x-page-head eyebrow="Individual payment" title="Pay only your quantity" lede="{{ $group->title }} · {{ $member->quantity }} {{ $group->product->unit }} of {{ $group->product->name }}." />
    <div class="split">
        <div class="panel">
            @if($order && $order->status === 'pending')
                <h2>Waiting for confirmation</h2>
                <p>Order {{ $order->number }} is pending. A bank transfer stays here until an administrator confirms it. You do not need to submit it again.</p>
                <a class="btn btn-line" href="{{ route('orders.show', $order) }}">Open the order</a>
            @else
                <form method="POST" action="{{ route('payments.store', $group) }}">
                    @csrf
                    <div class="field">
                        <label for="pay-method">Payment method</label>
                        <select id="pay-method" name="method" class="form-select" required>
                            <option value="card" @selected(old('method') === 'card')>Debit / Credit card</option>
                            <option value="jazzcash" @selected(old('method', 'jazzcash') === 'jazzcash')>JazzCash</option>
                            <option value="easypaisa" @selected(old('method') === 'easypaisa')>Easypaisa</option>
                            <option value="bank" @selected(old('method') === 'bank')>Bank transfer</option>
                        </select>
                    </div>
                    <div data-pay="card" class="d-none">
                        <div class="field">
                            <label for="card_number">Card number</label>
                            <input id="card_number" name="card_number" inputmode="numeric" autocomplete="off" class="form-control" placeholder="4242 4242 4242 4242">
                        </div>
                        <div class="row">
                            <div class="col-md-6 field">
                                <label for="expiry">Expiry</label>
                                <input id="expiry" name="expiry" class="form-control" placeholder="MM/YY" value="{{ old('expiry') }}">
                            </div>
                            <div class="col-md-6 field">
                                <label for="cvv">CVV</label>
                                <input id="cvv" name="cvv" class="form-control" autocomplete="off" placeholder="123">
                            </div>
                        </div>
                    </div>
                    <div data-pay="wallet">
                        <div class="field">
                            <label for="mobile">JazzCash or Easypaisa mobile</label>
                            <input id="mobile" name="mobile" value="{{ old('mobile', auth()->user()->phone) }}" class="form-control" placeholder="03XXXXXXXXX">
                        </div>
                    </div>
                    <div data-pay="bank" class="d-none">
                        <div class="field">
                            <label for="bank_reference">Transfer reference</label>
                            <input id="bank_reference" name="bank_reference" value="{{ old('bank_reference') }}" class="form-control" placeholder="HBL-123456">
                        </div>
                        <p class="muted">Bank transfers stay pending until an admin marks them paid.</p>
                    </div>
                    <div class="field">
                        <label for="city">City</label>
                        <input id="city" name="city" value="{{ old('city', auth()->user()->city) }}" class="form-control" placeholder="Lahore" required>
                    </div>
                    <div class="field">
                        <label for="shipping_address">Delivery address</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3" placeholder="House, street, area" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                    </div>
                    <button class="btn btn-brand" type="submit">Submit payment</button>
                </form>
            @endif
        </div>
        <aside class="quote-box">
            <p class="eyebrow">Amount due</p>
            <p class="big">@pkr($quote['line_total'])</p>
            <p>{{ $member->quantity }} × @pkr($quote['unit_price'])</p>
            <p>Shelf price would have been @pkr($quote['base_price'] * $member->quantity).</p>
            @if($quote['discount_percent'] > 0)
                <p class="save">You save @pkr($quote['savings']) at @pct($quote['discount_percent']) off.</p>
            @endif
            @if($group->status === 'open')
                <div class="feature-note">The group has not reached its target yet. Paying now locks today's unit price.</div>
            @endif
            <p class="muted mb-0">This checkout is a simulation for the university project. Card numbers are checked for shape and are not stored.</p>
        </aside>
    </div>
@endsection
