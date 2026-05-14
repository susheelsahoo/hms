<?php

namespace Modules\GlobalAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class SystemHealthStatistic extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'statistic_date' => 'date',
            'cache_hit_rate' => 'decimal:2',
            'error_rate' => 'decimal:2',
            'metadata' => 'array',
        ];
    }
}
