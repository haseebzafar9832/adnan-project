<a class="side-brand" href="{{ route('dashboard') }}">
    <span class="brand-mark"><i class="bi bi-box-seam"></i></span>
    <span class="brand-name">BulkShare</span>
</a>
<nav class="side-nav">
    <a class="side-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid"></i> Dashboard</a>
    <a class="side-link {{ request()->routeIs('groups.mine', 'groups.show', 'payments.*') ? 'is-active' : '' }}" href="{{ route('groups.mine') }}"><i class="bi bi-people"></i> My groups</a>
    <a class="side-link {{ request()->routeIs('groups.create') ? 'is-active' : '' }}" href="{{ route('groups.create') }}"><i class="bi bi-plus-circle"></i> Start a group</a>
    <a class="side-link {{ request()->routeIs('orders.*') ? 'is-active' : '' }}" href="{{ route('orders.index') }}"><i class="bi bi-truck"></i> Orders</a>
    <a class="side-link {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}" href="{{ route('notifications.index') }}">
        <i class="bi bi-bell"></i> Notices
        @if(($unreadNotifications ?? 0) > 0)
            <span class="side-count">{{ $unreadNotifications }}</span>
        @endif
    </a>
    <a class="side-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profile</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="side-link" type="submit"><i class="bi bi-box-arrow-right"></i> Log out</button>
    </form>
</nav>
<div class="side-foot">
    <div class="side-user">
        <span class="avatar tone-{{ auth()->user()->tone() }}">{{ auth()->user()->initials() }}</span>
        <span class="profile-meta">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
        </span>
    </div>
</div>
