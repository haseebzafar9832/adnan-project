@extends('layouts.base')
@php($onSite = ! auth()->check() || request()->routeIs('home', 'products.*', 'groups.index', 'groups.show'))
@section('body-class', $onSite ? 'site-body' : 'admin-body')

@section('body')
    @if (! $onSite)
        <div class="admin-shell">
            <aside class="admin-side d-none d-lg-block">
                @include('partials.desk-nav')
            </aside>
            <div class="admin-main">
                <div class="admin-top d-lg-none">
                    <button class="btn btn-line" type="button" data-bs-toggle="offcanvas" data-bs-target="#deskMenu">Menu</button>
                </div>
                @include('partials.flash')
                @yield('content')
            </div>
        </div>
        <div class="offcanvas offcanvas-start admin-side" tabindex="-1" id="deskMenu">
            <div class="offcanvas-body">
                @include('partials.desk-nav')
            </div>
        </div>
    @else
        <div class="topbar">Wholesale price, split across real people. Each member pays only for the quantity they take.</div>
        <header class="site-header">
            <nav class="navbar navbar-expand-lg site-nav">
                <div class="container">
                    <a class="brand navbar-brand" href="{{ route('home') }}">
                        <span class="brand-mark"><i class="bi bi-box-seam"></i></span>
                        <span>
                            <span class="brand-name">BulkShare</span>
                            <small>Buy together</small>
                        </span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Open menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="mainNav">
                        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('groups.index', 'groups.show') ? 'active' : '' }}" href="{{ route('groups.index') }}">Groups</a></li>
                            @auth
                                <li class="nav-item"><a class="nav-link" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}">Dashboard</a></li>
                                <li class="nav-item">@include('partials.profile-menu', ['showProfile' => ! auth()->user()->isAdmin()])</li>
                            @else
                                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log in</a></li>
                                <li class="nav-item"><a class="btn btn-brand btn-sm" href="{{ route('register') }}">Create account</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="site-main">
            <div class="container">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>
        <footer class="site-footer">
            <div class="container footer-grid">
                <div>
                    <strong>BulkShare</strong>
                    <p>Collaborative Sharing of Bulk Items Purchasing System. People pool a quantity, the price steps down, and every person pays only their own share.</p>
                </div>
                <div>
                    <p>Virtual University of Pakistan</p>
                    <p>Supervisor: Umra Naeem</p>
                    <p><a href="mailto:umra.naeem@vu.edu.pk">umra.naeem@vu.edu.pk</a></p>
                </div>
                <div>
                    <p>Card, JazzCash, Easypaisa, and bank transfer are simulated for this academic build. No real charge is made.</p>
                </div>
            </div>
        </footer>
    @endif
@endsection
