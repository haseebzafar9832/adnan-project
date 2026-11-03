@extends('layouts.admin')
@section('title', 'Discounts · Admin')

@section('content')
    <h1 class="serif">Discount rules</h1>
    <p class="lede">A tier with no product applies to every product that does not have its own ladder. Electronics in the demo use a separate ladder.</p>
    <div class="split">
        <form class="panel" method="POST" action="{{ route('admin.discounts.store') }}">
            @csrf
            <div class="field">
                <label for="name">Tier name</label>
                <input id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="Bulk" required>
            </div>
            <div class="field">
                <label for="product_id">Applies to</label>
                <select id="product_id" name="product_id" class="form-select">
                    <option value="">All products without a custom ladder</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 field">
                    <label for="min_quantity">Minimum quantity</label>
                    <input id="min_quantity" name="min_quantity" type="number" min="2" value="{{ old('min_quantity') }}" class="form-control" placeholder="25" required>
                </div>
                <div class="col-md-6 field">
                    <label for="discount_percent">Discount %</label>
                    <input id="discount_percent" name="discount_percent" type="number" min="1" max="80" step="0.01" value="{{ old('discount_percent') }}" class="form-control" placeholder="10" required>
                </div>
            </div>
            <button class="btn btn-brand" type="submit">Add tier</button>
        </form>
        <div class="table-card">
            <table class="table">
                <thead><tr><th>Tier</th><th>Scope</th><th>From</th><th>Off</th><th></th></tr></thead>
                <tbody>
                    @foreach($tiers as $tier)
                        <tr>
                            <td>{{ $tier->name }}</td>
                            <td>{{ $tier->appliesTo() }}</td>
                            <td>{{ $tier->min_quantity }}</td>
                            <td>@pct($tier->discount_percent)</td>
                            <td>
                                <form method="POST" action="{{ route('admin.discounts.destroy', $tier) }}" onsubmit="return confirm('Remove this tier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link text-danger" type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
