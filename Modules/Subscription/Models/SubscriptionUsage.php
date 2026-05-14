<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    protected $table = 'subscription_usages';

    protected $fillable = [
        'organization_id',
        'subscription_id',
        'hotels_used',
        'staff_used',
        'rooms_used',
        'bookings_used',
        'storage_used',
        'usage_period_start',
        'usage_period_end',
        'metadata',
    ];

    protected $casts = [
        'usage_period_start' => 'datetime',
        'usage_period_end' => 'datetime',
        'metadata' => 'json',
    ];

    public $timestamps = true;

    // Relationships
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    // Methods
    public function getUsagePercentage(string $resource): float
    {
        $plan = $this->subscription->plan;
        $used = $this->{''. $resource .'_used'} ?? 0;
        $limit = $plan->{''. $resource .'_limit'} ?? 1;

        if ($limit === 0) {
            return 0;
        }

        return ($used / $limit) * 100;
    }

    public function getRemainingQuota(string $resource): int
    {
        $plan = $this->subscription->plan;
        $used = $this->{''. $resource .'_used'} ?? 0;
        $limit = $plan->{''. $resource .'_limit'} ?? 0;

        return max(0, $limit - $used);
    }

    public function isResourceLimitExceeded(string $resource): bool
    {
        return $this->getRemainingQuota($resource) <= 0;
    }

    public function updateUsage(string $resource, int $increment = 1): void
    {
        $field = $resource . '_used';
        $this->increment($field, $increment);
    }

    public function resetMonthlyUsage(): void
    {
        $this->update([
            'bookings_used' => 0,
            'usage_period_start' => now(),
            'usage_period_end' => now()->addMonth(),
        ]);
    }
}
