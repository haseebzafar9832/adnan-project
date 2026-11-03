@extends('layouts.app')
@section('title', 'Log in · BulkShare')

@section('content')
    <div class="auth-wrap">
        <div class="auth-layout">
            <div class="panel auth-card">
                <p class="eyebrow">Welcome back</p>
                <h1>Log in</h1>
                <p class="muted auth-lede">Use your account to join a group, pay your share, and track the order.</p>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@email.com" required autofocus>
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input id="password" name="password" type="password" class="form-control" placeholder="Enter your password" required>
                            <button class="password-toggle" type="button" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1"> Keep me logged in
                    </label>
                    <button class="btn btn-brand auth-submit" type="submit">Log in</button>
                    <p class="auth-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
                </form>
            </div>
            <aside class="panel auth-card auth-side">
                <p class="eyebrow">Demo access</p>
                <h2>Examiner logins</h2>
                <p class="muted">Every account uses the password <span class="pw-chip">password</span></p>
                <ul class="demo-list">
                    <li><span>Admin</span><span>admin@bulkshare.com</span></li>
                    <li><span>Sara Malik</span><span>sara.malik@bulkshare.com</span></li>
                    <li><span>Bilal Hussain</span><span>bilal.hussain@bulkshare.com</span></li>
                    <li><span>Hina Qureshi</span><span>hina.qureshi@bulkshare.com</span></li>
                    <li><span>Usman Ali</span><span>usman.ali@bulkshare.com</span></li>
                </ul>
            </aside>
        </div>
    </div>
@endsection
