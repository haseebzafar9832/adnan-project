<div class="dropdown ms-auto">
    <button class="profile-chip" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" aria-label="{{ auth()->user()->name }}">
        <span class="avatar tone-{{ auth()->user()->tone() }}">{{ auth()->user()->initials() }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end profile-menu">
        @if($showProfile ?? false)
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profile</a></li>
            <li><hr class="dropdown-divider"></li>
        @endif
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right"></i> Log out</button>
            </form>
        </li>
    </ul>
</div>
