<div class="side-foot">
    <div class="side-user">
        <span class="avatar tone-{{ auth()->user()->tone() }}">{{ auth()->user()->initials() }}</span>
        <span class="profile-meta">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
        </span>
    </div>
    @if($showProfile ?? false)
        <a class="side-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profile</a>
    @endif
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="side-link" type="submit"><i class="bi bi-box-arrow-right"></i> Log out</button>
    </form>
</div>
