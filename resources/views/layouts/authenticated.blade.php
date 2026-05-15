@extends('layouts.app')

@section('body')
    @php
        $isUserManagementOpen = request()->routeIs('user-management.*');
        $isSubscriptionsOpen = request()->routeIs('super-admin.subscription-plans.*');
        $isGlobalAnalyticsOpen = request()->routeIs('super-admin.global-analytics.*');
    @endphp

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
                    <button
                        class="hms-nav-link hms-nav-toggle w-100 text-start mt-4 {{ $isUserManagementOpen ? 'active' : '' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#userManagementMenu"
                        aria-expanded="{{ $isUserManagementOpen ? 'true' : 'false' }}"
                        aria-controls="userManagementMenu"
                    >
                        <span>User Management</span>
                        <span class="hms-nav-toggle-icon" aria-hidden="true">›</span>
                    </button>
                    <div class="collapse {{ $isUserManagementOpen ? 'show' : '' }}" id="userManagementMenu">
                        <div class="ps-3 mt-1">
                            <a class="hms-nav-link {{ request()->routeIs('user-management.permissions.*') ? 'active' : '' }}" href="{{ route('user-management.permissions.index') }}">
                                Permissions
                            </a>
                            <a class="hms-nav-link {{ request()->routeIs('user-management.roles.*') ? 'active' : '' }}" href="{{ route('user-management.roles.index') }}">
                                Roles
                            </a>
                            <a class="hms-nav-link {{ request()->routeIs('user-management.users.*') ? 'active' : '' }}" href="{{ route('user-management.users.index') }}">
                                Users
                            </a>
                        </div>
                    </div>
                    <a class="hms-nav-link {{ $isSubscriptionsOpen ? 'active' : '' }}" href="{{ route('super-admin.subscription-plans.index') }}">Subscriptions</a>
                    <a class="hms-nav-link {{ $isGlobalAnalyticsOpen ? 'active' : '' }}" href="{{ route('super-admin.global-analytics.dashboard') }}">Global Analytics</a>
                @endif

                @if (auth()->user()->isHotelAdmin())
                    <div class="text-uppercase small text-white-50 mt-4 mb-2">Hotel Admin</div>
                    <a class="hms-nav-link {{ request()->routeIs('hotels.list') ? 'active' : '' }}" href="{{ route('hotels.list') }}">
                        <i class="bi bi-building"></i> My Hotels
                    </a>
                    <a class="hms-nav-link disabled" href="#">Rooms</a>
                    <a class="hms-nav-link disabled" href="#">Staff</a>
                    <a class="hms-nav-link disabled" href="#">Reports</a>
                @endif

                @if (auth()->user()->isHotelManager())
                    <div class="text-uppercase small text-white-50 mt-4 mb-2">Hotel Manager</div>
                    <a class="hms-nav-link {{ request()->routeIs('hotels.list') ? 'active' : '' }}" href="{{ route('hotels.list') }}">
                        <i class="bi bi-building"></i> My Hotels
                    </a>
                    <a class="hms-nav-link disabled" href="#">Bookings</a>
                    <a class="hms-nav-link disabled" href="#">Check-in</a>
                    <a class="hms-nav-link disabled" href="#">Guests</a>
                    <a class="hms-nav-link disabled" href="#">Availability</a>
                @endif
            </nav>
        </aside>

        <div class="hms-main">
            <nav class="navbar bg-white border-bottom px-3">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        Menu
                    </button>
                    <span class="navbar-brand mb-0 h1 ms-2">@yield('page-title', 'Dashboard')</span>
                    
                    @php
                        $userHotels = [];
                        if ((auth()->user()->isOrganizationOwner() || auth()->user()->isHotelAdmin() || auth()->user()->isHotelManager()) && method_exists(\Modules\Hotel\Services\HotelService::class, 'getUserHotels')) {
                            $hotelService = app(\Modules\Hotel\Services\HotelService::class);
                            $userHotels = $hotelService->getUserHotels(auth()->user());
                        }
                    @endphp

                    @if ($userHotels && count($userHotels) > 0)
                        <div class="dropdown ms-auto d-none d-md-block">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-building"></i> 
                                {{ session('selected_hotel_name', $userHotels[0]->name ?? 'Select Hotel') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach ($userHotels as $hotel)
                                    <li>
                                        @if (session('selected_hotel_id') === $hotel->id)
                                            <a class="dropdown-item active" href="#">
                                                <i class="bi bi-check-circle"></i> {{ $hotel->name }}
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('hotels.select', $hotel) }}" class="d-inline w-100">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-building"></i> {{ $hotel->name }}
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('hotels.list') }}">
                                        <i class="bi bi-list"></i> View All Hotels
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
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
                <button
                    class="hms-nav-link hms-nav-toggle w-100 text-start mt-2 {{ $isUserManagementOpen ? 'active' : '' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mobileUserManagementMenu"
                    aria-expanded="{{ $isUserManagementOpen ? 'true' : 'false' }}"
                    aria-controls="mobileUserManagementMenu"
                >
                    <span>User Management</span>
                    <span class="hms-nav-toggle-icon" aria-hidden="true">›</span>
                </button>
                <div class="collapse {{ $isUserManagementOpen ? 'show' : '' }}" id="mobileUserManagementMenu">
                    <div class="ps-3 mt-1">
                        <a class="hms-nav-link {{ request()->routeIs('user-management.permissions.*') ? 'active' : '' }}" href="{{ route('user-management.permissions.index') }}">Permissions</a>
                        <a class="hms-nav-link {{ request()->routeIs('user-management.roles.*') ? 'active' : '' }}" href="{{ route('user-management.roles.index') }}">Roles</a>
                        <a class="hms-nav-link {{ request()->routeIs('user-management.users.*') ? 'active' : '' }}" href="{{ route('user-management.users.index') }}">Users</a>
                    </div>
                </div>
                <a class="hms-nav-link {{ $isSubscriptionsOpen ? 'active' : '' }}" href="{{ route('super-admin.subscription-plans.index') }}">Subscriptions</a>
                <a class="hms-nav-link {{ $isGlobalAnalyticsOpen ? 'active' : '' }}" href="{{ route('super-admin.global-analytics.dashboard') }}">Global Analytics</a>
            @endif
        </div>
    </div>
@endsection
