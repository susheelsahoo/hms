<?php

namespace Modules\GlobalAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueStatistic extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'statistic_date' => 'date',
            'total_revenue' => 'decimal:2',
            'monthly_revenue' => 'decimal:2',
            'annual_revenue' => 'decimal:2',
            'refunds' => 'decimal:2',
            'metadata' => 'array',
        ];
    }
}
