<?php

namespace Modules\GlobalAnalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Hotel\Models\Hotel;
use Modules\Organization\Models\Organization;

class BookingStatistic extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'statistic_date' => 'date',
            'total_revenue' => 'decimal:2',
            'average_booking_value' => 'decimal:2',
            'occupancy_rate' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
