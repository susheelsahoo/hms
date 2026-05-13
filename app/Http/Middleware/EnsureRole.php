<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user()?->loadMissing('role');

        abort_if(! $user || ! in_array($user->role?->slug, $roles, true), 403);

        return $next($request);
    }
}
