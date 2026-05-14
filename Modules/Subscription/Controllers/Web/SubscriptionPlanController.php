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
