<?php

namespace Modules\GlobalAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDaily extends Model
{
    protected $table = 'analytics_daily';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'analytics_date' => 'date',
            'total_revenue' => 'decimal:2',
            'metadata' => 'array',
        ];
    }
}
