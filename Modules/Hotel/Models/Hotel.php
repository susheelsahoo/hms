<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Room\Models\Room;
use Modules\Room\Models\RateType;
use Modules\Room\Models\RoomType;
use Modules\Organization\Models\Organization;
use Modules\Shared\Models\TenantModel;
use Modules\User\Models\User;

class Hotel extends TenantModel
{
    protected $guarded = ['id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function rateTypes(): HasMany
    {
        return $this->hasMany(RateType::class);
    }
}
