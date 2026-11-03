@extends('layouts.app')
@section('title', 'Create account · BulkShare')

@section('content')
    <div class="auth-wrap">
    <div class="auth-layout">
        <div class="panel">
            <p class="eyebrow">New buyer</p>
            <h1 class="serif">Create your account</h1>
            <form method="POST" action="{{ route('register') }}" class="mt-4">
                @csrf
                <div class="field">
                    <label for="name">Full name</label>
                    <input id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="Your full name" required>
                </div>
                <div class="row">
                    <div class="col-md-6 field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="you@email.com" required>
                    </div>
                    <div class="col-md-6 field">
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="03XXXXXXXXX">
                    </div>
                </div>
                <div class="field">
                    <label for="city">City</label>
                    <input id="city" name="city" value="{{ old('city') }}" class="form-control" placeholder="Lahore">
                </div>
                <div class="field">
                    <label for="address">Delivery address</label>
                    <textarea id="address" name="address" class="form-control" rows="2" placeholder="House, street, area">{{ old('address') }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 field">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input id="password" type="password" name="password" class="form-control" placeholder="Create a password" required>
                            <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6 field">
                        <label for="password_confirmation">Confirm password</label>
                        <div class="password-field">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Repeat the password" required>
                            <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-brand" type="submit">Create account</button>
                <a class="ms-2" href="{{ route('login') }}">I already have an account</a>
            </form>
        </div>
        <aside class="panel">
            <h2>What you can do</h2>
            <p>Join a group with the quantity you need, message the other members, and pay that share through a simulated checkout.</p>
            <p class="mb-0 muted">An administrator can add products, set the discount ladder, confirm bank transfers, and move orders through delivery.</p>
        </aside>
    </div>
    </div>
@endsection
