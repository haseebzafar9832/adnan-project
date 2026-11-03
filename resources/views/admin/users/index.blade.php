@extends('layouts.admin')
@section('title', 'Users · Admin')

@section('content')
    <h1 class="serif">Users</h1>
    <form class="search-row mb-3" method="GET">
        <input class="form-control" style="max-width: 320px" type="search" name="q" value="{{ request('q') }}" placeholder="Name or email">
        <button class="btn btn-line" type="submit">Search</button>
    </form>
    <div class="table-card">
        <table class="table">
            <thead><tr><th>Person</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            <div class="muted">{{ $user->email }}{{ $user->city ? ' · '.$user->city : '' }}</div>
                        </td>
                        <td colspan="3">
                            <form class="d-flex flex-wrap gap-2 align-items-center" method="POST" action="{{ route('admin.users.update', $user) }}">
                                @csrf
                                @method('PUT')
                                <select class="form-select" style="max-width: 140px" name="role">
                                    <option value="user" @selected($user->role === 'user')>Buyer</option>
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                </select>
                                <select class="form-select" style="max-width: 160px" name="is_active">
                                    <option value="1" @selected($user->is_active)>Active</option>
                                    <option value="0" @selected(! $user->is_active)>Deactivated</option>
                                </select>
                                <button class="btn btn-brand btn-sm" type="submit">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
@endsection
