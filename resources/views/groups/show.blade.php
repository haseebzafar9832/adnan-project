@extends('layouts.app')
@section('title', $group->title.' · BulkShare')

@section('content')
    <div class="group-layout">
        <section>
            <div class="panel mb-3">
                @include('partials.product-art', ['product' => $group->product])
                <div class="deal-top mt-3">
                    <x-status :status="$group->status" :label="$group->statusLabel()" />
                    <span class="muted">Closes {{ $group->deadline->format('d M Y, h:i A') }}</span>
                </div>
                <p class="eyebrow mt-3">{{ $group->product->category->name }}</p>
                <h1 class="serif">{{ $group->title }}</h1>
                <p>{{ $group->description }}</p>
                <p><a href="{{ route('products.show', $group->product) }}">{{ $group->product->name }}</a> · organised by {{ $group->creator->name }}</p>
                <div class="meter"><span style="width: {{ $group->progressPercent() }}%"></span></div>
                <div class="deal-meta">
                    <strong>{{ $group->current_quantity }} / {{ $group->target_quantity }} {{ $group->product->unit }}</strong>
                    <span>{{ $group->progressPercent() }}% of target</span>
                </div>
                <p class="mt-3 mb-0"><span class="price-was">@pkr($group->product->base_price)</span> <span class="price-now">@pkr($group->liveQuote()['unit_price'])</span> per {{ $group->product->unit }}</p>
                @if($group->liveQuote()['discount_percent'] > 0)
                    <p class="save">@pct($group->liveQuote()['discount_percent']) off on the current pledged quantity{{ $group->liveQuote()['tier_name'] ? ' · '.$group->liveQuote()['tier_name'].' tier' : '' }}.</p>
                @endif
                @if($group->liveQuote()['next_tier'])
                    <div class="feature-note">{{ $group->liveQuote()['next_tier']['units_needed'] }} more {{ \App\Support\Money::units($group->product->unit, $group->liveQuote()['next_tier']['units_needed']) }} unlocks {{ $group->liveQuote()['next_tier']['name'] }} at @pct($group->liveQuote()['next_tier']['discount_percent']).</div>
                @endif
                @if(auth()->id() === $group->user_id && $group->isJoinable())
                    <form class="mt-3" method="POST" action="{{ route('groups.close', $group) }}" onsubmit="return confirm('Close this group to new members?');">
                        @csrf
                        <button class="btn btn-line btn-sm" type="submit">Close group</button>
                    </form>
                @endif
            </div>

            <div class="panel mb-3">
                <h2>Price ladder</h2>
                @foreach($tiers as $tier)
                    <div class="tier {{ (int) $group->liveQuote()['applied_tier_id'] === (int) $tier->id ? 'is-active' : '' }} mb-2">
                        <strong>@pct($tier->discount_percent)</strong>
                        <span>{{ $tier->name }} · from {{ $tier->min_quantity }} {{ $group->product->unit }}</span>
                    </div>
                @endforeach
                <p class="muted mb-0">The target is the group's goal. A discount tier can unlock earlier, as soon as the pledged quantity crosses it.</p>
            </div>

            <div class="panel" id="message-panel"
                 data-poll="{{ route('messages.poll', $group) }}"
                 data-last="{{ $messages->max('id') ?? 0 }}"
                 data-member="{{ $membership ? 1 : 0 }}">
                <h2>Group messages</h2>
                @if($membership)
                    <div class="thread">
                        @forelse($messages as $message)
                            <div class="msg {{ $message->user_id === auth()->id() ? 'is-mine' : '' }}" data-id="{{ $message->id }}">
                                <div class="msg-top">
                                    <strong>{{ $message->user->name }}</strong>
                                    <time>{{ $message->created_at->diffForHumans() }}</time>
                                </div>
                                <p>{{ $message->body }}</p>
                            </div>
                        @empty
                            <p class="muted">No messages yet. Say hello to the group.</p>
                        @endforelse
                    </div>
                    <form id="message-form" class="composer" method="POST" action="{{ route('messages.store', $group) }}">
                        @csrf
                        <textarea class="form-control" name="body" maxlength="1000" placeholder="Write to the group" required></textarea>
                        <button class="btn btn-brand" type="submit">Send</button>
                    </form>
                @else
                    <p class="muted mb-0">Join the group to message the other members.</p>
                @endif
            </div>
        </section>

        <aside>
            <div class="panel mb-3">
                <h2>Members</h2>
                @foreach($group->members as $member)
                    <div class="member">
                        <div class="who">
                            <span class="avatar-sm tone-{{ $member->user->tone() }}">{{ $member->user->initials() }}</span>
                            <div>
                                <strong>{{ $member->user->name }}</strong>
                                @if($member->user_id === $group->user_id)<span class="muted"> · organiser</span>@endif
                                <div class="muted">{{ $member->quantity }} {{ $group->product->unit }}{{ $member->user->city ? ' · '.$member->user->city : '' }}</div>
                            </div>
                        </div>
                        <x-status :status="$member->payment_status" :label="$member->paymentLabel()" />
                    </div>
                @endforeach
            </div>

            <div class="panel">
                @if($membership)
                    <h2>Your share</h2>
                    <p>{{ $membership->quantity }} {{ $group->product->unit }}</p>
                    @if($membership->payment_status === 'paid')
                        <p class="save">Paid at @pkr($membership->locked_unit_price) per {{ $group->product->unit }}.</p>
                        <a class="btn btn-line" href="{{ route('orders.index') }}">View orders</a>
                    @elseif($membership->payment_status === 'refunded')
                        <p>This share was refunded. You can pay again if the group is still valid.</p>
                        <a class="btn btn-brand" href="{{ route('payments.show', $group) }}">Pay again</a>
                    @else
                        <p>Current unit price <strong>@pkr($membership->locked_unit_price ?? $group->liveQuote()['unit_price'])</strong></p>
                        <a class="btn btn-brand" href="{{ route('payments.show', $group) }}">Pay your share</a>
                    @endif
                @elseif($group->isJoinable())
                    <h2>Join this group</h2>
                    @auth
                        <form method="POST" action="{{ route('groups.join', $group) }}">
                            @csrf
                            <div id="quote-preview"
                                 data-mode="join"
                                 data-url="{{ route('pricing.quote') }}"
                                 data-product="{{ $group->product_id }}"
                                 data-current="{{ $group->current_quantity }}">
                                <p class="eyebrow">Live price</p>
                                <p class="big" data-q="unit">@pkr($joinQuote['unit_price']) / {{ $group->product->unit }}</p>
                                <p data-q="line">@pkr($joinQuote['line_total']) for 1 {{ $group->product->unit }}</p>
                                <p data-q="off">{{ $joinQuote['discount_percent'] > 0 ? '' : '' }}@if($joinQuote['discount_percent'] > 0) @pct($joinQuote['discount_percent']) off @if($joinQuote['tier_name']) · {{ $joinQuote['tier_name'] }} tier @endif @else Shelf price. The first tier is still ahead. @endif</p>
                                <p data-q="next">
                                    @if($joinQuote['next_tier'])
                                        {{ $joinQuote['next_tier']['units_needed'] }} more {{ \App\Support\Money::units($group->product->unit, $joinQuote['next_tier']['units_needed']) }} unlocks {{ $joinQuote['next_tier']['name'] }} at @pct($joinQuote['next_tier']['discount_percent']).
                                    @else
                                        This is the best published tier.
                                    @endif
                                </p>
                            </div>
                            <div class="field mt-3">
                                <label for="quantity">Your quantity</label>
                                <input id="quantity" name="quantity" type="number" min="1" max="500" value="{{ old('quantity', 1) }}" class="form-control" required>
                            </div>
                            <button class="btn btn-brand" type="submit">Join group</button>
                        </form>
                    @else
                        <p>Log in to add your quantity. The price preview updates as the total grows.</p>
                        <a class="btn btn-brand" href="{{ route('login') }}">Log in to join</a>
                    @endauth
                @else
                    <h2>Joining is closed</h2>
                    <p class="mb-0 muted">This group is {{ strtolower($group->statusLabel()) }}. Existing members can still open their orders.</p>
                @endif
            </div>
        </aside>
    </div>
@endsection
