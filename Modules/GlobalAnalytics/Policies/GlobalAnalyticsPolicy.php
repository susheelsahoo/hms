<?php

namespace Modules\GlobalAnalytics\Policies;

use Modules\User\Models\User;

class GlobalAnalyticsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
