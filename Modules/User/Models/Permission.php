<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Role\Models\Role;

class Permission extends Model
{
    public const MANAGE_HOTELS = 'manage_hotels';
    public const MANAGE_ROOMS = 'manage_rooms';
    public const MANAGE_BOOKINGS = 'manage_bookings';
    public const MANAGE_PAYMENTS = 'manage_payments';
    public const VIEW_REPORTS = 'view_reports';
    public const MANAGE_STAFF = 'manage_staff';
    public const APPROVE_REFUNDS = 'approve_refunds';

    protected $guarded = ['id'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')
            ->withTimestamps();
    }
}
