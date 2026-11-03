@extends('layouts.app')
@section('title', 'Profile · BulkShare')

@section('content')
    <x-page-head eyebrow="Account" title="Profile" lede="This address is copied into checkout, and you can still change it on each order." />
    <div class="split">
        <form class="panel" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" placeholder="Your full name" required>
            </div>
            <div class="row">
                <div class="col-md-6 field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" placeholder="you@email.com" required>
                </div>
                <div class="col-md-6 field">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" placeholder="03XXXXXXXXX">
                </div>
            </div>
            <div class="field">
                <label for="city">City</label>
                <input id="city" name="city" value="{{ old('city', $user->city) }}" class="form-control" placeholder="Lahore">
            </div>
            <div class="field">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="3" placeholder="House, street, area">{{ old('address', $user->address) }}</textarea>
            </div>
            <button class="btn btn-brand" type="submit">Save profile</button>
        </form>
        <form class="panel" method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            <h2>Password</h2>
            <div class="field">
                <label for="current_password">Current password</label>
                <div class="password-field">
                    <input id="current_password" type="password" name="current_password" class="form-control" placeholder="Current password" required>
                    <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="field">
                <label for="password">New password</label>
                <div class="password-field">
                    <input id="password" type="password" name="password" class="form-control" placeholder="New password" required>
                    <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <div class="password-field">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Repeat the new password" required>
                    <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <button class="btn btn-line" type="submit">Change password</button>
        </form>
    </div>
@endsection
