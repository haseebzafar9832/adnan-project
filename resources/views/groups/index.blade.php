@extends('layouts.app')
@section('title', 'Bulk groups · BulkShare')

@section('content')
    <x-page-head eyebrow="Pools" title="Bulk groups" lede="Join a pool that is still collecting, or look back at groups that have closed.">
        @auth
            <a class="btn btn-brand" href="{{ route('groups.create') }}">Start a group</a>
        @endauth
    </x-page-head>
    <div class="filters">
        @foreach(['active' => 'Collecting', 'open' => 'Open', 'reached' => 'Target reached', 'fulfilled' => 'Fulfilled', 'closed' => 'Closed', 'cancelled' => 'Cancelled'] as $key => $label)
            <a class="{{ $status === $key ? 'is-on' : '' }}" href="{{ route('groups.index', ['status' => $key, 'q' => request('q')]) }}">{{ $label }}</a>
        @endforeach
    </div>
    <form class="search-row mb-3" method="GET">
        <input type="hidden" name="status" value="{{ $status }}">
        <input class="form-control" style="max-width: 320px" type="search" name="q" value="{{ request('q') }}" placeholder="Search a group or product">
        <button class="btn btn-line" type="submit">Search</button>
    </form>
    @if($groups->isEmpty())
        <div class="panel empty">No groups in this view.</div>
    @else
        <div class="card-grid">
            @foreach($groups as $group)
                @include('groups._card', ['group' => $group])
            @endforeach
        </div>
        <div class="mt-3">{{ $groups->links() }}</div>
    @endif
@endsection
