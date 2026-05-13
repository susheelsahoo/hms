@extends('layouts.authenticated')

@section('title', 'Dashboard | HMS')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <span class="badge text-bg-primary mb-3">{{ str_replace('_', ' ', $user->role?->slug ?? 'user') }}</span>
                    <h1 class="h3 mb-2">Welcome, {{ $user->first_name }} {{ $user->last_name }}</h1>
                    <p class="text-secondary mb-0">
                        You are logged in to the Hotel Management SaaS dashboard.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6">Organizations</h2>
                    <p class="text-secondary mb-0">Manage tenant organizations and ownership.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6">Hotels</h2>
                    <p class="text-secondary mb-0">Manage assigned hotel operations.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6">Bookings</h2>
                    <p class="text-secondary mb-0">Track bookings, payments, and invoices.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
