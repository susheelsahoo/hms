<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Room\Models\Room;
use Modules\Shared\Models\TenantModel;
use Modules\User\Models\User;

class Hotel extends TenantModel
{
    protected $guarded = ['id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_hotels')
            ->withPivot(['organization_id', 'access_type', 'is_primary'])
            ->withTimestamps();
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
