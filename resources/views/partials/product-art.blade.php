@php($category = $product->category)
@if($product->imageUrl())
    <img class="cover" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">
@else
    <div class="art art-{{ $category->slug ?? 'general' }}">
        <i class="bi bi-{{ $category->icon ?? 'bag' }}"></i>
        <span>{{ $product->unit }}</span>
    </div>
@endif
