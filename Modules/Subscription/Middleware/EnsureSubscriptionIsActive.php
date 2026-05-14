<?php

namespace Modules\Subscription\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Subscription\Exceptions\SubscriptionException;
use Modules\Subscription\Services\SubscriptionService;

class EnsureSubscriptionIsActive
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next)
    {
        $organizationId = $request->user()?->organization_id;

        if (!$organizationId) {
            return $next($request);
        }

        $subscription = $this->subscriptionService->getActiveSubscription($organizationId);

        if (!$subscription || !$subscription->isActive()) {
            throw SubscriptionException::subscriptionExpired();
        }

        return $next($request);
    }
}
