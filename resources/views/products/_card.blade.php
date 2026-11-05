<a class="product-card" href="{{ route('products.show', $product) }}">
    @include('partials.product-art', ['product' => $product])
    <div class="card-copy">
        <div class="product-meta">
            <span class="muted">{{ $product->category->name }}</span>
            <strong class="price-tag">@pkr($product->base_price)</strong>
        </div>
        <h3>{{ $product->name }}</h3>
        <p class="muted mb-0">{{ $product->summary }}</p>
    </div>
</a>
