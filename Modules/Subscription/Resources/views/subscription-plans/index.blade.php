@extends('layouts.authenticated')

@section('title', 'Subscription Plans | HMS')
@section('page-title', 'Subscription Plans')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Subscription Plans</h1>
            <p class="text-secondary mb-0">Activate, pause, and review SaaS plans available to organizations.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Plan</th>
                        <th>Pricing</th>
                        <th>Limits</th>
                        <th>Features</th>
                        <th>Subscriptions</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $plan->name }}</div>
                                <div class="small text-secondary">{{ $plan->slug }}</div>
                                @if ($plan->description)
                                    <div class="small text-secondary mt-1">{{ $plan->description }}</div>
                                @endif
                            </td>
                            <td>
                                <div>${{ number_format((float) $plan->price_monthly, 2) }} / month</div>
                                <div class="small text-secondary">${{ number_format((float) $plan->price_yearly, 2) }} / year</div>
                            </td>
                            <td>
                                <div class="small">Hotels: {{ number_format($plan->hotel_limit) }}</div>
                                <div class="small">Staff: {{ number_format($plan->staff_limit) }}</div>
                                <div class="small">Rooms: {{ number_format($plan->room_limit) }}</div>
                                <div class="small">Bookings: {{ number_format($plan->booking_limit) }}</div>
                            </td>
                            <td>{{ $plan->features_count }}</td>
                            <td>{{ $plan->subscriptions_count }}</td>
                            <td>
                                <span class="badge text-bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                    {{ $plan->is_active ? 'active' : 'inactive' }}
                                </span>
                                @if ($plan->is_trial)
                                    <span class="badge text-bg-info ms-1">trial</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('super-admin.subscription-plans.status.update', $plan) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $plan->is_active ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $plan->is_active ? 'secondary' : 'success' }}">
                                        {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                No subscription plans found. Run <code>php artisan db:seed --class=Modules\\Subscription\\Database\\Seeders\\SubscriptionPlanSeeder</code>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $plans->links() }}</div>
@endsection
