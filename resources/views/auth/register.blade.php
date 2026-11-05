@extends('layouts.app')
@section('title', 'Create account · BulkShare')

@section('content')
    <div class="auth-stage auth-stage-wide">
        <aside class="auth-visual" style="background-image: url('{{ asset('images/banner/bulk-buying.jpg') }}')">
            <div class="auth-visual-copy">
                <a class="auth-brand" href="{{ route('home') }}"><span class="brand-mark"><i class="bi bi-box-seam"></i></span> BulkShare</a>
                <p class="eyebrow">New buyer</p>
                <h2>Open an account and start a pool.</h2>
                <ul>
                    <li><i class="bi bi-box-seam"></i><span>Pick a product and set your quantity.</span></li>
                    <li><i class="bi bi-chat-dots"></i><span>Message the people in your group.</span></li>
                    <li><i class="bi bi-truck"></i><span>Pay your share, then track delivery.</span></li>
                </ul>
            </div>
        </aside>
        <section class="auth-panel">
            <p class="eyebrow">Create account</p>
            <h1>Tell us where the share should go</h1>
            <p class="auth-lede">Name, email, and a password are enough to join. Add a phone and address so delivery is ready when you pay.</p>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="field">
                    <label for="name">Full name</label>
                    <input id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Your full name" required>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="auth-grid">
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@email.com" required>
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="03XXXXXXXXX">
                        @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="field">
                    <label for="city">City</label>
                    <input id="city" name="city" value="{{ old('city') }}" class="form-control @error('city') is-invalid @enderror" placeholder="Lahore">
                    @error('city')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="address">Delivery address</label>
                    <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="House, street, area">{{ old('address') }}</textarea>
                    @error('address')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="auth-grid">
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="At least 8 characters" required>
                            <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirm password</label>
                        <div class="password-field">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Repeat the password" required>
                            <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-brand auth-submit" type="submit">Create account</button>
                <p class="auth-switch">Already registered? <a href="{{ route('login') }}">Log in</a></p>
            </form>
        </section>
    </div>
@endsection
