<?php

namespace Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\User\Models\Permission;
use Modules\User\Models\User;

class Role extends Model
{
    public const SUPER_ADMIN = 'super_admin';
    public const ORGANIZATION_OWNER = 'organization_owner';
    public const HOTEL_ADMIN = 'hotel_admin';
    public const HOTEL_MANAGER = 'hotel_manager';
    public const STAFF = 'staff';
    public const RECEPTIONIST = 'receptionist';
    public const ACCOUNTANT = 'accountant';

    public $timestamps = true;

    protected $guarded = ['id'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }
}
