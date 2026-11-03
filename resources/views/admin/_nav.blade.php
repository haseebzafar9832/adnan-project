<a class="side-brand" href="{{ route('admin.dashboard') }}">
    <span class="brand-mark"><i class="bi bi-box-seam"></i></span>
    <span class="brand-name">BulkShare</span>
</a>
<nav class="side-nav">
    <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid"></i> Dashboard</a>
    <a class="side-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Users</a>
    <a class="side-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
    <a class="side-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i> Products</a>
    <a class="side-link {{ request()->routeIs('admin.discounts.*') ? 'is-active' : '' }}" href="{{ route('admin.discounts.index') }}"><i class="bi bi-percent"></i> Discounts</a>
    <a class="side-link {{ request()->routeIs('admin.groups.*') ? 'is-active' : '' }}" href="{{ route('admin.groups.index') }}"><i class="bi bi-collection"></i> Groups</a>
    <a class="side-link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-truck"></i> Orders</a>
    <a class="side-link {{ request()->routeIs('admin.transactions.*') ? 'is-active' : '' }}" href="{{ route('admin.transactions.index') }}"><i class="bi bi-credit-card"></i> Transactions</a>
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
