@extends('layouts.admin')
@section('title', 'Categories · Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="serif mb-0">Categories</h1>
        <a class="btn btn-brand" href="{{ route('admin.categories.create') }}">Add category</a>
    </div>
    <div class="table-card">
        <table class="table">
            <thead><tr><th>Category</th><th>Products</th><th>Visible</th><th></th></tr></thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td><i class="bi bi-{{ $category->icon }}"></i> <strong>{{ $category->name }}</strong><div class="muted">{{ $category->description }}</div></td>
                        <td>{{ $category->products_count }}</td>
                        <td>{{ $category->is_active ? 'Yes' : 'Hidden' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');">
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
@endsection
