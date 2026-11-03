@extends('layouts.app')
@section('title', 'Notifications · BulkShare')

@section('content')
    <x-page-head eyebrow="Alerts" title="Notifications" lede="Group milestones, payment updates, and delivery changes land here.">
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button class="btn btn-line" type="submit">Mark all read</button>
        </form>
    </x-page-head>
    <div class="panel">
        @forelse($notifications as $notification)
            <a class="member text-decoration-none" href="{{ route('notifications.open', $notification) }}">
                <div>
                    <strong>{{ $notification->title }}</strong>
                    @if($notification->isUnread())<span class="status status-reached">New</span>@endif
                    <div class="muted">{{ $notification->body }}</div>
                </div>
                <span class="muted">{{ $notification->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <p class="empty mb-0">You are up to date.</p>
        @endforelse
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
@endsection
