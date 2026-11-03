@extends('layouts.app')
@section('title', 'Products · BulkShare')

@section('content')
    <x-page-head eyebrow="Catalogue" title="Products" lede="Filter by category or search. Prices shown here are the shelf price, before a group discount." />
    <form id="product-filter" class="search-row mb-3" method="GET" action="{{ route('products.index') }}">
        <input class="form-control" style="max-width: 320px" type="search" name="q" value="{{ request('q') }}" placeholder="Search rice, paper, earbuds">
        <select class="form-select" style="max-width: 240px" name="category">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-line" type="submit">Search</button>
    </form>
    <div class="filters">
        <a class="{{ request('category') ? '' : 'is-on' }}" href="{{ route('products.index', request()->except('category', 'page')) }}">All</a>
        @foreach($categories as $category)
            <a class="{{ request('category') === $category->slug ? 'is-on' : '' }}" href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}">{{ $category->name }}</a>
        @endforeach
    </div>
    <div id="product-grid">
        @include('products._grid', ['products' => $products])
    </div>
@endsection
