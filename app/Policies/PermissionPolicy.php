<?php

namespace App\Policies;

use Modules\User\Models\Permission;
use Modules\User\Models\User;

class PermissionPolicy
{
    public function before(User $user): bool|null
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::MANAGE_STAFF);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::MANAGE_STAFF);
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermission(Permission::MANAGE_STAFF);
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermission(Permission::MANAGE_STAFF);
    }
}
