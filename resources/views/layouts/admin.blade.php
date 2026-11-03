@extends('layouts.base')
@section('body-class', 'admin-body')

@section('body')
    <div class="admin-shell">
        <aside class="admin-side d-none d-lg-block">
            @include('admin._nav')
        </aside>
        <div class="admin-main">
            <div class="admin-top d-lg-none">
                <button class="btn btn-line" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMenu">Menu</button>
            </div>
            @include('partials.flash')
            @yield('content')
        </div>
    </div>
    <div class="offcanvas offcanvas-start admin-side" tabindex="-1" id="adminMenu">
        <div class="offcanvas-body">
            @include('admin._nav')
        </div>
    </div>
@endsection
