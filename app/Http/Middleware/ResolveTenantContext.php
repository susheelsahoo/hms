<?php

namespace App\Http\Middleware;

use App\Support\Context\HotelContext;
use App\Support\Context\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResolveTenantContext
{
    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly HotelContext $hotelContext,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $this->tenantContext->setUser($user);
        $organizationHeader = config('tenancy.headers.organization');
        $hotelHeader = config('tenancy.headers.hotel');

        $this->tenantContext->setOrganizationId(
            ($organizationHeader ? $request->integer($organizationHeader) : null)
                ?: $user?->organization_id
        );
        $this->hotelContext->setHotelId(
            ($hotelHeader ? $request->integer($hotelHeader) : null) ?: null
        );

        return $next($request);
    }
}
