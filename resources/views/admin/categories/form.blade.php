@extends('layouts.admin')
@section('title', ($category->exists ? 'Edit' : 'Add').' category · Admin')

@section('content')
    <h1 class="serif">{{ $category->exists ? 'Edit category' : 'Add category' }}</h1>
    <form class="panel" style="max-width: 680px" method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if($category->exists) @method('PUT') @endif
        <div class="field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $category->name) }}" class="form-control" placeholder="Groceries" required>
        </div>
        <div class="field">
            <label for="icon">Icon</label>
            <select id="icon" name="icon" class="form-select" required>
                @foreach($icons as $icon => $label)
                    <option value="{{ $icon }}" @selected(old('icon', $category->icon) === $icon)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Short description of this category">{{ old('description', $category->description) }}</textarea>
        </div>
        <div class="field">
            <label for="is_active">Visibility</label>
            <select id="is_active" name="is_active" class="form-select">
                <option value="1" @selected(old('is_active', $category->is_active ?? true))>Visible</option>
                <option value="0" @selected(old('is_active', $category->exists ? $category->is_active : true) === false || old('is_active') === '0')>Hidden</option>
            </select>
        </div>
        <button class="btn btn-brand" type="submit">Save</button>
        <a class="btn btn-line" href="{{ route('admin.categories.index') }}">Cancel</a>
    </form>
@endsection
