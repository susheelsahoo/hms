<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Subscription\Database\Factories\SubscriptionPlanFactory;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'hotel_limit',
        'staff_limit',
        'room_limit',
        'booking_limit',
        'storage_limit',
        'features',
        'is_trial',
        'trial_days',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'features' => 'json',
        'metadata' => 'json',
        'is_trial' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function newFactory()
    {
        return SubscriptionPlanFactory::new();
    }

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function features()
    {
        return $this->hasMany(SubscriptionFeature::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTrialPlans($query)
    {
        return $query->where('is_trial', true);
    }

    public function scopeRegularPlans($query)
    {
        return $query->where('is_trial', false);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    // Methods
    public function getMonthlyPrice(): float
    {
        return (float) $this->price_monthly;
    }

    public function getYearlyPrice(): float
    {
        return (float) $this->price_yearly;
    }

    public function getYearlySavings(): float
    {
        $monthlyTotal = $this->getMonthlyPrice() * 12;
        $yearlySavings = $monthlyTotal - $this->getYearlyPrice();
        return $yearlySavings > 0 ? $yearlySavings : 0;
    }

    public function getSavingsPercentage(): float
    {
        if ($this->getMonthlyPrice() === 0) {
            return 0;
        }
        return ($this->getYearlySavings() / ($this->getMonthlyPrice() * 12)) * 100;
    }

    public function hasFeature(string $featureKey): bool
    {
        return $this->features()
            ->where('feature_key', $featureKey)
            ->where('is_included', true)
            ->exists();
    }

    public function getFeatureLimit(string $featureKey, string $limitKey): ?int
    {
        $feature = $this->features()
            ->where('feature_key', $featureKey)
            ->first();

        return $feature ? $feature->limits[$limitKey] ?? null : null;
    }
}
