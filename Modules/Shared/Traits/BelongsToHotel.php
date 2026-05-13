<?php

namespace Modules\Shared\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Hotel\Models\Hotel;

trait BelongsToHotel
{
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
