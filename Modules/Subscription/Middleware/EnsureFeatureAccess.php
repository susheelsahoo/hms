<?php

namespace Modules\Subscription\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Subscription\Exceptions\SubscriptionException;
use Modules\Subscription\Services\SubscriptionService;

class EnsureFeatureAccess
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next, string $feature)
    {
        $organizationId = $request->user()?->organization_id;

        if (!$organizationId) {
            return $next($request);
        }

        if (!$this->subscriptionService->hasFeatureAccess($organizationId, $feature)) {
            throw SubscriptionException::featureNotAvailable($feature);
        }

        return $next($request);
    }
}
