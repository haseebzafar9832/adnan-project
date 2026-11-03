@php($category = $product->category)
@if($product->image)
    <img class="cover" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
@else
    <div class="art art-{{ $category->slug ?? 'general' }}">
        <i class="bi bi-{{ $category->icon ?? 'bag' }}"></i>
        <span>{{ $product->unit }}</span>
    </div>
@endif
