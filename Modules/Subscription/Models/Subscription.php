<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Models\Organization;
use Modules\Subscription\Database\Factories\SubscriptionFactory;
use Modules\Subscription\Enums\SubscriptionStatus;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscriptions';

    protected $fillable = [
        'organization_id',
        'subscription_plan_id',
        'status',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'grace_ends_at',
        'cancelled_at',
        'renewal_at',
        'amount',
        'currency',
        'auto_renew',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'grace_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'renewal_at' => 'datetime',
        'amount' => 'decimal:2',
        'auto_renew' => 'boolean',
        'metadata' => 'json',
        'status' => SubscriptionStatus::class,
    ];

    protected $dates = [
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'grace_ends_at',
        'cancelled_at',
        'renewal_at',
    ];

    protected static function newFactory()
    {
        return SubscriptionFactory::new();
    }

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function usage()
    {
        return $this->hasOne(SubscriptionUsage::class);
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function histories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::ACTIVE);
    }

    public function scopeTrial($query)
    {
        return $query->where('status', SubscriptionStatus::TRIAL);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', SubscriptionStatus::EXPIRED);
    }

    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeAutoRenewal($query)
    {
        return $query->where('auto_renew', true);
    }

    public function scopeNotAutoRenewal($query)
    {
        return $query->where('auto_renew', false);
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVE;
    }

    public function isTrial(): bool
    {
        return $this->status === SubscriptionStatus::TRIAL;
    }

    public function isExpired(): bool
    {
        return $this->status === SubscriptionStatus::EXPIRED;
    }

    public function isPastDue(): bool
    {
        return $this->status === SubscriptionStatus::PAST_DUE;
    }

    public function isCancelled(): bool
    {
        return $this->status === SubscriptionStatus::CANCELLED;
    }

    public function isSuspended(): bool
    {
        return $this->status === SubscriptionStatus::SUSPENDED;
    }

    public function isTrialActive(): bool
    {
        if (! $this->isTrial()) {
            return false;
        }

        return $this->trial_ends_at?->isFuture() ?? false;
    }

    public function isTrialEnded(): bool
    {
        return $this->isTrial() && $this->trial_ends_at?->isPast() ?? false;
    }

    public function daysRemainingInTrial(): ?int
    {
        if (! $this->isTrialActive()) {
            return null;
        }

        return now()->diffInDays($this->trial_ends_at, false);
    }

    public function daysUntilExpiration(): ?int
    {
        if (! $this->isActive()) {
            return null;
        }

        return now()->diffInDays($this->ends_at, false);
    }

    public function isRenewable(): bool
    {
        return $this->auto_renew && $this->renewal_at?->isPast() ?? false;
    }

    public function hasGracePeriod(): bool
    {
        return ! is_null($this->grace_ends_at) && $this->grace_ends_at->isFuture();
    }

    public function canUpgrade(): bool
    {
        return ! $this->isCancelled();
    }

    public function canDowngrade(): bool
    {
        return ! $this->isCancelled();
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [
            SubscriptionStatus::ACTIVE,
            SubscriptionStatus::TRIAL,
            SubscriptionStatus::PAST_DUE,
        ]);
    }

    public function getAmount(): float
    {
        return (float) $this->amount;
    }

    public function getPlanLimits(): array
    {
        return [
            'hotels' => $this->plan->hotel_limit,
            'staff' => $this->plan->staff_limit,
            'rooms' => $this->plan->room_limit,
            'bookings' => $this->plan->booking_limit,
            'storage' => $this->plan->storage_limit,
        ];
    }

    public function hasFeature(string $featureKey): bool
    {
        return $this->plan->hasFeature($featureKey);
    }
}
