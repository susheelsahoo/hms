<?php

namespace Modules\GlobalAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionStatistic extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'statistic_date' => 'date',
            'churn_rate' => 'decimal:2',
            'conversion_rate' => 'decimal:2',
            'metadata' => 'array',
        ];
    }
}
