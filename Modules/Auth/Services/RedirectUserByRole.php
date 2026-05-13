<?php

namespace Modules\Auth\Services;

use Modules\User\Models\User;

class RedirectUserByRole
{
    public function routeName(User $user): string
    {
        return match ($user->role?->slug) {
            'super_admin' => 'super-admin.dashboard',
            'hotel_admin' => 'hotel-admin.dashboard',
            'hotel_manager' => 'hotel-manager.dashboard',
            default => 'dashboard',
        };
    }
}
