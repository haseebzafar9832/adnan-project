@if($products->isEmpty())
    <div class="panel empty">No products match this search. Try another category or a shorter name.</div>
@else
    <div class="card-grid">
        @foreach($products as $product)
            @include('products._card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-3">{{ $products->links() }}</div>
@endif
