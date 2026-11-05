@extends('layouts.app')
@section('title', 'Log in · BulkShare')

@section('content')
    <div class="auth-stage">
        <aside class="auth-visual" style="background-image: url('{{ asset('images/banner/bulk-buying.jpg') }}')">
            <div class="auth-visual-copy">
                <a class="auth-brand" href="{{ route('home') }}"><span class="brand-mark"><i class="bi bi-box-seam"></i></span> BulkShare</a>
                <h2>The price falls when the order grows.</h2>
                <ul>
                    <li><i class="bi bi-people"></i><span>Join a pool for only what you need.</span></li>
                    <li><i class="bi bi-graph-down-arrow"></i><span>Watch the unit price step down.</span></li>
                    <li><i class="bi bi-bag-check"></i><span>Pay your share and track that order.</span></li>
                </ul>
            </div>
        </aside>
        <section class="auth-panel">
            <p class="eyebrow">Welcome back</p>
            <h1>Log in</h1>
            <p class="auth-lede">Use your account to join a group, pay your share, and follow the delivery.</p>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@email.com" required autofocus>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                        <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <label class="remember">
                    <input type="checkbox" name="remember" value="1"> Keep me logged in
                </label>
                <button class="btn btn-brand auth-submit" type="submit">Log in</button>
                <p class="auth-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
            </form>
        </section>
    </div>
@endsection
