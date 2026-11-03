@extends('layouts.app')
@section('title', 'Start a group · BulkShare')

@section('content')
    <x-page-head eyebrow="Organiser" title="Start a bulk group" lede="You join with your own quantity. The target must be higher, so there is room for other buyers." />
    <div class="split">
        <form class="panel" method="POST" action="{{ route('groups.store') }}">
            @csrf
            <div class="field">
                <label for="product_id">Product</label>
                <select id="product_id" name="product_id" class="form-select" required>
                    <option value="">Choose a product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected((int) $productId === (int) $product->id)>{{ $product->name }} · @pkr($product->base_price)</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="title">Group title</label>
                <input id="title" name="title" value="{{ old('title') }}" class="form-control" maxlength="120" required placeholder="Hostel rice pool">
            </div>
            <div class="field">
                <label for="description">Note for members</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Who this group is for, and when you plan to place the order">{{ old('description') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4 field">
                    <label for="my_quantity">Your quantity</label>
                    <input id="my_quantity" name="my_quantity" type="number" min="1" max="500" value="{{ old('my_quantity', $mine) }}" class="form-control" required>
                </div>
                <div class="col-md-4 field">
                    <label for="target_quantity">Target quantity</label>
                    <input id="target_quantity" name="target_quantity" type="number" min="2" max="5000" value="{{ old('target_quantity', $target) }}" class="form-control" required>
                </div>
                <div class="col-md-4 field">
                    <label for="deadline">Deadline</label>
                    <input id="deadline" name="deadline" type="datetime-local" value="{{ old('deadline', now()->addDays(14)->format('Y-m-d\TH:i')) }}" class="form-control" required>
                </div>
            </div>
            <button class="btn btn-brand" type="submit">Publish group</button>
        </form>
        <aside class="quote-box" id="quote-preview" data-mode="create" data-url="{{ route('pricing.quote') }}" @if($productId) data-product="{{ $productId }}" @endif>
            <p class="eyebrow">Price preview</p>
            <h2>What the ladder would charge</h2>
            <p>If only you have joined<br><strong data-q="solo">@if($preview) @pkr($preview['solo']['unit_price']) · @if($preview['solo']['discount_percent'] > 0) @pct($preview['solo']['discount_percent']) off @else shelf price @endif @else Choose a product @endif</strong></p>
            <p>If the target is met<br><strong data-q="target">@if($preview) @pkr($preview['target']['unit_price']) · @if($preview['target']['discount_percent'] > 0) @pct($preview['target']['discount_percent']) off @else shelf price @endif @else — @endif</strong></p>
            <p data-q="save">@if($preview) Your {{ $mine }} would cost @pkr($preview['target']['line_total']) if the target is met. @endif</p>
        </aside>
    </div>
@endsection
