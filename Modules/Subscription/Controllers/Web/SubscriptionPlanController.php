<?php

namespace Modules\Subscription\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanController
{
    public function index(): View
    {
        return view('subscription::subscription-plans.index', [
            'plans' => SubscriptionPlan::query()
                ->withCount(['subscriptions', 'features'])
                ->orderBy('price_monthly')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('subscription::subscription-plans.create', [
            'plan' => new SubscriptionPlan(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subscription_plans'],
            'slug' => ['required', 'string', 'max:255', 'unique:subscription_plans'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'price_yearly' => ['required', 'numeric', 'min:0'],
            'hotel_limit' => ['required', 'integer', 'min:-1'],
            'staff_limit' => ['required', 'integer', 'min:-1'],
            'room_limit' => ['required', 'integer', 'min:-1'],
            'booking_limit' => ['required', 'integer', 'min:-1'],
            'storage_limit' => ['required', 'integer', 'min:-1'],
            'is_trial' => ['boolean'],
            'trial_days' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        SubscriptionPlan::create($validated);

        return redirect()
            ->route('super-admin.subscription-plans.index')
            ->with('status', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan): View
    {
        return view('subscription::subscription-plans.edit', [
            'plan' => $subscriptionPlan,
        ]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subscription_plans,name,'.$subscriptionPlan->id],
            'slug' => ['required', 'string', 'max:255', 'unique:subscription_plans,slug,'.$subscriptionPlan->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'price_yearly' => ['required', 'numeric', 'min:0'],
            'hotel_limit' => ['required', 'integer', 'min:-1'],
            'staff_limit' => ['required', 'integer', 'min:-1'],
            'room_limit' => ['required', 'integer', 'min:-1'],
            'booking_limit' => ['required', 'integer', 'min:-1'],
            'storage_limit' => ['required', 'integer', 'min:-1'],
            'is_trial' => ['boolean'],
            'trial_days' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $subscriptionPlan->update($validated);

        return redirect()
            ->route('super-admin.subscription-plans.index')
            ->with('status', 'Subscription plan updated successfully.');
    }

    public function updateStatus(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $subscriptionPlan->update([
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('super-admin.subscription-plans.index')
            ->with('status', 'Subscription plan status updated successfully.');
    }
}
