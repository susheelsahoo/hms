<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Hotel\Models\Hotel;
use Modules\Shared\Models\TenantModel;
use Modules\Shared\Traits\BelongsToHotel;

class RateType extends TenantModel
{
    use BelongsToHotel;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }
}

