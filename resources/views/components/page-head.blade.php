@props(['title', 'eyebrow' => null, 'lede' => null])
<div class="page-head">
    <div>
        @if($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif
        <h1>{{ $title }}</h1>
        @if($lede)<p class="lede">{{ $lede }}</p>@endif
    </div>
    @if(! $slot->isEmpty())
        <div class="page-actions">{{ $slot }}</div>
    @endif
</div>
