@extends('layouts.admin')
@section('title', 'Groups · Admin')

@section('content')
    <h1 class="serif">Bulk groups</h1>
    <div class="filters">
        <a class="{{ request('status') ? '' : 'is-on' }}" href="{{ route('admin.groups.index') }}">All</a>
        @foreach(['open' => 'Open', 'reached' => 'Reached', 'closed' => 'Closed', 'fulfilled' => 'Fulfilled', 'cancelled' => 'Cancelled'] as $key => $label)
            <a class="{{ request('status') === $key ? 'is-on' : '' }}" href="{{ route('admin.groups.index', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="table-card">
        <table class="table">
            <thead><tr><th>Group</th><th>Progress</th><th>Deadline</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>
                            <a href="{{ route('groups.show', $group) }}"><strong>{{ $group->title }}</strong></a>
                            <div class="muted">{{ $group->product->name }} · {{ $group->creator->name }}</div>
                        </td>
                        <td>{{ $group->current_quantity }}/{{ $group->target_quantity }}</td>
                        <td>{{ $group->deadline->format('d M Y') }}</td>
                        <td><x-status :status="$group->status" :label="$group->statusLabel()" /></td>
                        <td class="text-end">
                            @if(in_array($group->status, ['open', 'reached']))
                                <form class="d-inline" method="POST" action="{{ route('admin.groups.close', $group) }}">
                                    @csrf
                                    <button class="btn btn-line btn-sm" type="submit">Close</button>
                                </form>
                            @endif
                            @if(! in_array($group->status, ['fulfilled', 'cancelled']))
                                <form class="d-inline" method="POST" action="{{ route('admin.groups.cancel', $group) }}" onsubmit="return confirm('Cancel this group? Paid members must be refunded first.');">
                                    @csrf
                                    <button class="btn btn-link text-danger" type="submit">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $groups->links() }}</div>
@endsection
