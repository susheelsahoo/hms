@extends('layouts.authenticated')

@section('title', 'Global Analytics | HMS')
@section('page-title', 'Global Analytics')

@section('content')
    @php
        $widgets = $analytics['widgets'] ?? [];
        $platform = $analytics['platform'] ?? [];
        $health = $analytics['system_health'] ?? [];
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Global Analytics</h1>
            <p class="text-secondary mb-0">Platform-wide SaaS, booking, revenue, subscription, and health metrics.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">Total Revenue</div>
                    <div class="fs-4 fw-semibold">${{ number_format((float) ($widgets['total_revenue'] ?? 0), 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">Total Bookings</div>
                    <div class="fs-4 fw-semibold">{{ number_format((int) ($widgets['total_bookings'] ?? 0)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">MRR / ARR</div>
                    <div class="fs-5 fw-semibold">${{ number_format((float) ($widgets['mrr'] ?? 0), 2) }}</div>
                    <div class="small text-secondary">${{ number_format((float) ($widgets['arr'] ?? 0), 2) }} annual</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">Platform Health</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) ($widgets['platform_health'] ?? 0), 1) }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Platform Snapshot</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="text-secondary small">Organizations</div>
                            <div class="fw-semibold">{{ number_format((int) ($platform['total_organizations'] ?? 0)) }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-secondary small">Hotels</div>
                            <div class="fw-semibold">{{ number_format((int) ($platform['total_hotels'] ?? 0)) }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-secondary small">Rooms</div>
                            <div class="fw-semibold">{{ number_format((int) ($platform['total_rooms'] ?? 0)) }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-secondary small">Users</div>
                            <div class="fw-semibold">{{ number_format((int) ($platform['total_users'] ?? 0)) }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-secondary small">Active Subscriptions</div>
                            <div class="fw-semibold">{{ number_format((int) ($platform['active_subscriptions'] ?? 0)) }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-secondary small">Trial Subscriptions</div>
                            <div class="fw-semibold">{{ number_format((int) ($platform['trial_subscriptions'] ?? 0)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">System Health</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-secondary">Queue Jobs Pending</span>
                        <strong>{{ number_format((int) ($health['queue_jobs_pending'] ?? 0)) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-secondary">Failed Jobs</span>
                        <strong>{{ number_format((int) ($health['failed_jobs'] ?? 0)) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-secondary">Error Rate</span>
                        <strong>{{ number_format((float) ($health['error_rate'] ?? 0), 2) }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
