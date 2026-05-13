<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Hotel\Models\Hotel;
use Modules\Shared\Models\TenantModel;
use Modules\Shared\Traits\BelongsToHotel;

class Room extends TenantModel
{
    use BelongsToHotel;

    protected $guarded = ['id'];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
