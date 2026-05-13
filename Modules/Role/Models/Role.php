<?php

namespace Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\User\Models\User;

class Role extends Model
{
    public const SUPER_ADMIN = 'super_admin';
    public const HOTEL_ADMIN = 'hotel_admin';
    public const HOTEL_MANAGER = 'hotel_manager';

    public $timestamps = true;

    protected $guarded = ['id'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
