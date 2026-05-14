<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    protected $table = 'subscription_features';

    protected $fillable = [
        'subscription_plan_id',
        'feature_key',
        'feature_name',
        'description',
        'is_included',
        'limits',
        'metadata',
    ];

    protected $casts = [
        'is_included' => 'boolean',
        'limits' => 'json',
        'metadata' => 'json',
    ];

    public $timestamps = true;

    // Relationships
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Scopes
    public function scopeIncluded($query)
    {
        return $query->where('is_included', true);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('feature_key', $key);
    }

    // Methods
    public function isIncluded(): bool
    {
        return $this->is_included;
    }

    public function getLimit(string $key): ?int
    {
        return $this->limits[$key] ?? null;
    }

    public function setLimit(string $key, int $value): void
    {
        $limits = $this->limits ?? [];
        $limits[$key] = $value;
        $this->update(['limits' => $limits]);
    }
}
