@extends('layouts.app')
@section('title', $product->name.' · BulkShare')

@section('content')
    <div class="product-layout">
        <section class="panel">
            @include('partials.product-art', ['product' => $product])
            <p class="eyebrow mt-3">{{ $product->category->name }}</p>
            <h1 class="serif">{{ $product->name }}</h1>
            <p class="lede">{{ $product->summary }}</p>
            <p>{{ $product->description }}</p>
            <p class="mb-0"><strong>Shelf price @pkr($product->base_price)</strong> per {{ $product->unit }}</p>
        </section>
        <aside>
            <div class="panel mb-3">
                <h2>Price ladder</h2>
                @forelse($tiers as $tier)
                    <div class="member">
                        <div>
                            <strong>{{ $tier->name }}</strong>
                            <div class="muted">From {{ $tier->min_quantity }} {{ $product->unit }}</div>
                        </div>
                        <strong>@pct($tier->discount_percent)</strong>
                    </div>
                @empty
                    <p class="muted">No discount tiers yet. The shelf price applies.</p>
                @endforelse
                @auth
                    <a class="btn btn-brand mt-3" href="{{ route('groups.create', ['product' => $product->slug]) }}">Start a group</a>
                @else
                    <a class="btn btn-brand mt-3" href="{{ route('login') }}">Log in to start a group</a>
                @endauth
            </div>
            <div class="panel">
                <h2>Open groups</h2>
                @forelse($groups as $group)
                    <div class="member">
                        <div>
                            <a href="{{ route('groups.show', $group) }}"><strong>{{ $group->title }}</strong></a>
                            <div class="muted">{{ $group->current_quantity }}/{{ $group->target_quantity }} · @pkr($group->liveQuote()['unit_price'])</div>
                        </div>
                        <x-status :status="$group->status" :label="$group->statusLabel()" />
                    </div>
                @empty
                    <p class="muted mb-0">Nobody has opened a group for this product yet.</p>
                @endforelse
            </div>
        </aside>
    </div>
@endsection
