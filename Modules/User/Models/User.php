<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Hotel\Models\Hotel;
use Modules\Organization\Models\Organization;
use Modules\Role\Models\Role;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'metadata' => 'array',
            'password' => 'hashed',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'user_hotels')
            ->withPivot(['organization_id', 'access_type', 'is_primary'])
            ->withTimestamps();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === Role::SUPER_ADMIN;
    }

    public function isHotelAdmin(): bool
    {
        return $this->role?->slug === Role::HOTEL_ADMIN;
    }

    public function isHotelManager(): bool
    {
        return $this->role?->slug === Role::HOTEL_MANAGER;
    }
}
