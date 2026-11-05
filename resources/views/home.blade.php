@extends('layouts.app')
@section('title', 'BulkShare · Buy together, pay your share')

@section('content')
    <section class="hero">
        <div>
            <p class="eyebrow">Collaborative bulk buying</p>
            <h1>The more neighbours join, the less everyone pays.</h1>
            <p class="hero-copy">Suppliers cut the price when the quantity is large. One household usually cannot get there alone. BulkShare lets people pledge what they actually need, watches the total, and drops the unit price as the ladder is climbed. Each person pays only their own share.</p>
            <div class="hero-actions">
                <a class="btn btn-brand" href="{{ route('products.index') }}">Browse products</a>
                <a class="btn btn-line" href="{{ route('groups.index') }}">See open groups</a>
            </div>
            <ul class="stat-row">
                <li><strong>{{ number_format($stats['buyers']) }}</strong><span>buyers on the platform</span></li>
                <li><strong>{{ number_format($stats['groups']) }}</strong><span>bulk groups opened</span></li>
                <li><strong>@pkr($stats['saved'])</strong><span>saved against shelf price</span></li>
            </ul>
        </div>
        @if($featured)
            <article class="feature-card">
                @include('partials.product-art', ['product' => $featured->product])
                <div class="feature-kicker mt-3">
                    <span>Live group</span>
                    <span>{{ $featured->deadline->diffForHumans() }}</span>
                </div>
                <p class="muted mb-0">{{ $featured->product->category->name }} · organised by {{ $featured->creator->name }}</p>
                <h2>{{ $featured->title }}</h2>
                <p>{{ $featured->product->name }}</p>
                <div class="meter"><span style="width: {{ $featured->progressPercent() }}%"></span></div>
                <div class="deal-meta">
                    <span>{{ $featured->current_quantity }} of {{ $featured->target_quantity }} {{ $featured->product->unit }}</span>
                    <span><span class="price-was">@pkr($featured->product->base_price)</span><span class="price-now">@pkr($featured->liveQuote()['unit_price'])</span></span>
                </div>
                @if($featured->liveQuote()['next_tier'])
                    <div class="feature-note">{{ $featured->liveQuote()['next_tier']['units_needed'] }} more {{ \App\Support\Money::units($featured->product->unit, $featured->liveQuote()['next_tier']['units_needed']) }} unlocks {{ $featured->liveQuote()['next_tier']['discount_percent'] }}% off.</div>
                @endif
                <a class="btn btn-gold mt-3" href="{{ route('groups.show', $featured) }}">Open this group</a>
            </article>
        @endif
    </section>

    <section class="bulk-banner" style="background-image: url('{{ asset('images/banner/bulk-buying.jpg') }}')">
        <div class="bulk-banner-copy">
            <p class="eyebrow">Bulk buying</p>
            <h2>One large order. A lower price for every share.</h2>
            <p>Join a pool for rice, oil, electronics, or stationery. The unit price drops as the quantity climbs, and you pay only for what you take.</p>
            <a class="btn btn-gold" href="{{ route('products.index') }}">Browse the catalogue</a>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Categories</p>
                <h2>Shop by what you already buy</h2>
            </div>
        </div>
        <div class="category-grid">
            @foreach($categories as $category)
                <a class="cat-tile" href="{{ route('products.index', ['category' => $category->slug]) }}">
                    @if($category->imageUrl())
                        <img class="cat-photo" src="{{ $category->imageUrl() }}" alt="{{ $category->name }}">
                    @else
                        <i class="bi bi-{{ $category->icon }}"></i>
                    @endif
                    <span class="cat-copy">
                        <strong>{{ $category->name }}</strong>
                        <span>{{ $category->products_count }} products</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Catalogue</p>
                <h2>Products ready to pool</h2>
            </div>
            <a href="{{ route('products.index') }}">View all products</a>
        </div>
        <div class="card-grid">
            @foreach($products as $product)
                @include('products._card', ['product' => $product])
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Open now</p>
                <h2>Groups collecting quantity</h2>
            </div>
            <a href="{{ route('groups.index') }}">View all groups</a>
        </div>
        <div class="card-grid">
            @forelse($groups as $group)
                @include('groups._card', ['group' => $group])
            @empty
                <div class="panel empty">No open groups yet. Be the first to start one.</div>
            @endforelse
        </div>
    </section>

    <section class="section" id="how">
        <div class="section-head">
            <div>
                <p class="eyebrow">How it works</p>
                <h2>Four steps, one shared order</h2>
            </div>
        </div>
        <div class="step-grid">
            <article class="step"><em>01</em><h3>Make an account</h3><p>Register with the address you want the share delivered to.</p></article>
            <article class="step"><em>02</em><h3>Start or join</h3><p>Set a target and a deadline, or add your quantity to a group that already exists.</p></article>
            <article class="step"><em>03</em><h3>Watch the price</h3><p>The unit price updates from the pledged total. Tiers can unlock before the target is met.</p></article>
            <article class="step"><em>04</em><h3>Pay your share</h3><p>Card, JazzCash, Easypaisa, or bank. Then track that order on its own.</p></article>
        </div>
    </section>

    @if($tiers->isNotEmpty())
        <section class="section">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Price ladder</p>
                    <h2>Discounts follow the quantity, not a coupon code</h2>
                </div>
            </div>
            <div class="tier-grid">
                @foreach($tiers as $tier)
                    <article class="tier">
                        <span class="muted">{{ $tier->name }}</span>
                        <strong>@pct($tier->discount_percent)</strong>
                        <p class="mb-0">From {{ $tier->min_quantity }} units. Electronics use a separate ladder.</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="band">
        <div>
            <h2>Need something that is not in a group yet?</h2>
            <p>Pick a product, name the pool, and decide how much you will take yourself.</p>
        </div>
        <a class="btn btn-gold" href="{{ auth()->check() ? route('groups.create') : route('register') }}">Start a bulk group</a>
    </section>
@endsection
