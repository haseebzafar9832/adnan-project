<a class="deal-card" href="{{ route('groups.show', $group) }}">
    <div class="deal-top">
        <x-status :status="$group->status" :label="$group->statusLabel()" />
        <span class="muted">{{ $group->members_count ?? $group->members->count() }} members</span>
    </div>
    <h3>{{ $group->title }}</h3>
    <p class="mb-1">{{ $group->product->name }}</p>
    <div class="meter" aria-hidden="true"><span style="width: {{ $group->progressPercent() }}%"></span></div>
    <div class="deal-meta">
        <span>{{ $group->current_quantity }} / {{ $group->target_quantity }} {{ $group->product->unit }}</span>
        <strong>@pkr($group->liveQuote()['unit_price'])</strong>
    </div>
    @if($group->liveQuote()['discount_percent'] > 0)
        <p class="save">@pct($group->liveQuote()['discount_percent']) off the shelf price of @pkr($group->product->base_price)</p>
    @else
        <p class="muted mb-0">Still at the shelf price. The first tier is ahead.</p>
    @endif
</a>
