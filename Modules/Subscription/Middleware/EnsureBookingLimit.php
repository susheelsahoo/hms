<?php

namespace Modules\Subscription\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Subscription\Services\LimitValidator;

class EnsureBookingLimit
{
    public function __construct(private LimitValidator $validator) {}

    public function handle(Request $request, Closure $next)
    {
        $organizationId = $request->user()?->organization_id;

        if ($organizationId && $request->route()->getName() === 'bookings.create') {
            $this->validator->validateAndTrackBooking($organizationId);
        }

        return $next($request);
    }
}
