<a class="product-card d-block" href="{{ route('products.show', $product) }}">
    @include('partials.product-art', ['product' => $product])
    <div class="product-meta mt-3">
        <span class="muted">{{ $product->category->name }}</span>
        <strong>@pkr($product->base_price)</strong>
    </div>
    <h3>{{ $product->name }}</h3>
    <p class="muted mb-0">{{ $product->summary }}</p>
</a>
