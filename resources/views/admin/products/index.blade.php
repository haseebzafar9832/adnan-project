@extends('layouts.admin')
@section('title', 'Products · Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="serif mb-0">Products</h1>
        <a class="btn btn-brand" href="{{ route('admin.products.create') }}">Add product</a>
    </div>
    <form class="search-row mb-3" method="GET">
        <input class="form-control" style="max-width: 320px" type="search" name="q" value="{{ request('q') }}" placeholder="Product name">
        <button class="btn btn-line" type="submit">Search</button>
    </form>
    <div class="table-card">
        <table class="table">
            <thead><tr><th>Product</th><th>Category</th><th>Shelf price</th><th>Visible</th><th></th></tr></thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td><strong>{{ $product->name }}</strong><div class="muted">per {{ $product->unit }}</div></td>
                        <td>{{ $product->category->name }}</td>
                        <td>@pkr($product->base_price)</td>
                        <td>{{ $product->is_active ? 'Yes' : 'Hidden' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete or hide this product?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link text-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $products->links() }}</div>
@endsection
