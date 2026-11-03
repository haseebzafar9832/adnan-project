@extends('layouts.admin')
@section('title', ($product->exists ? 'Edit' : 'Add').' product · Admin')

@section('content')
    <h1 class="serif">{{ $product->exists ? 'Edit product' : 'Add product' }}</h1>
    <form class="panel" style="max-width: 760px" method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if($product->exists) @method('PUT') @endif
        <div class="field">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-select" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $product->name) }}" class="form-control" placeholder="Super Basmati Rice 5kg" required>
        </div>
        <div class="field">
            <label for="summary">Short summary</label>
            <input id="summary" name="summary" value="{{ old('summary', $product->summary) }}" class="form-control" maxlength="180" placeholder="One line a buyer sees on the card" required>
        </div>
        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="What the buyer is pooling, and why the bulk price is lower" required>{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-4 field">
                <label for="unit">Unit</label>
                <input id="unit" name="unit" value="{{ old('unit', $product->unit) }}" class="form-control" placeholder="bag, piece, pack" required>
            </div>
            <div class="col-md-4 field">
                <label for="base_price">Shelf price (Rs)</label>
                <input id="base_price" name="base_price" type="number" min="1" step="0.01" value="{{ old('base_price', $product->base_price) }}" class="form-control" placeholder="2200" required>
            </div>
            <div class="col-md-4 field">
                <label for="is_active">Visibility</label>
                <select id="is_active" name="is_active" class="form-select">
                    <option value="1" @selected(old('is_active', $product->is_active ? '1' : '0') == '1')>Visible</option>
                    <option value="0" @selected(old('is_active', $product->is_active ? '1' : '0') == '0')>Hidden</option>
                </select>
            </div>
        </div>
        <div class="field">
            <label for="image">Photo</label>
            <input id="image" name="image" type="file" accept="image/*" class="form-control">
            @if($product->image)
                <img class="mt-2 cover" style="height: 120px; width: 180px" src="{{ asset('storage/'.$product->image) }}" alt="">
            @endif
        </div>
        <button class="btn btn-brand" type="submit">Save product</button>
        <a class="btn btn-line" href="{{ route('admin.products.index') }}">Cancel</a>
    </form>
@endsection
