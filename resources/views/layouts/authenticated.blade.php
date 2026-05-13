@extends('layouts.app')

@section('body')
    <div class="hms-shell">
        <aside class="hms-sidebar bg-dark text-white">
            <div class="px-3 py-4 border-bottom border-secondary">
                <a href="{{ route('dashboard') }}" class="text-white text-decoration-none fw-semibold fs-5">HMS</a>
                <div class="small text-white-50 mt-1">{{ auth()->user()->role?->name }}</div>
            </div>

            <nav class="p-3">
                <div class="text-uppercase small text-white-50 mb-2">Main</div>
                <a class="hms-nav-link {{ request()->routeIs('dashboard', '*.dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                @if (auth()->user()->isSuperAdmin())
                    <div class="text-uppercase small text-white-50 mt-4 mb-2">Super Admin</div>
                    <a class="hms-nav-link {{ request()->routeIs('super-admin.organizations.*') ? 'active' : '' }}" href="{{ route('super-admin.organizations.index') }}">
                        Organizations
                    </a>
                    <a class="hms-nav-link {{ request()->routeIs('super-admin.organizations.hotels.*') ? 'active' : '' }}" href="{{ route('super-admin.organizations.index') }}">Hotels</a>
                    <a class="hms-nav-link disabled" href="#">Users</a>
                    <a class="hms-nav-link disabled" href="#">Subscriptions</a>
                    <a class="hms-nav-link disabled" href="#">Global Analytics</a>
                @endif

                @if (auth()->user()->isHotelAdmin())
                    <div class="text-uppercase small text-white-50 mt-4 mb-2">Hotel Admin</div>
                    <a class="hms-nav-link disabled" href="#">My Hotels</a>
                    <a class="hms-nav-link disabled" href="#">Rooms</a>
                    <a class="hms-nav-link disabled" href="#">Staff</a>
                    <a class="hms-nav-link disabled" href="#">Reports</a>
                @endif

                @if (auth()->user()->isHotelManager())
                    <div class="text-uppercase small text-white-50 mt-4 mb-2">Hotel Manager</div>
                    <a class="hms-nav-link disabled" href="#">Bookings</a>
                    <a class="hms-nav-link disabled" href="#">Check-in</a>
                    <a class="hms-nav-link disabled" href="#">Guests</a>
                    <a class="hms-nav-link disabled" href="#">Availability</a>
                @endif
            </nav>
        </aside>

        <div class="hms-main">
            <nav class="navbar bg-white border-bottom px-3">
                <div>
                    <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        Menu
                    </button>
                    <span class="navbar-brand mb-0 h1 ms-2">@yield('page-title', 'Dashboard')</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-secondary small d-none d-md-inline">{{ auth()->user()->email }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                    </form>
                </div>
            </nav>

            <main class="p-3 p-lg-4">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">HMS</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <a class="hms-nav-link {{ request()->routeIs('dashboard', '*.dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
            @if (auth()->user()->isSuperAdmin())
                <a class="hms-nav-link {{ request()->routeIs('super-admin.organizations.*') ? 'active' : '' }}" href="{{ route('super-admin.organizations.index') }}">Organizations</a>
            @endif
        </div>
    </div>
@endsection
