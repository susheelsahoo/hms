<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Room\Models\Room;
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
}
