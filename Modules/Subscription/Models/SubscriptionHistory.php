<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Subscription\Enums\SubscriptionAction;

class SubscriptionHistory extends Model
{
    protected $table = 'subscription_histories';

    protected $fillable = [
        'organization_id',
        'subscription_id',
        'old_plan_id',
        'new_plan_id',
        'action',
        'description',
        'changed_by',
        'metadata',
    ];

    protected $casts = [
        'action' => SubscriptionAction::class,
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;
    public $incrementing = true;

    // Relationships
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function oldPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'old_plan_id');
    }

    public function newPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'new_plan_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }

    // Scopes
    public function scopeByAction($query, SubscriptionAction $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc');
    }

    // Methods
    public function isUpgrade(): bool
    {
        return $this->action === SubscriptionAction::UPGRADE;
    }

    public function isDowngrade(): bool
    {
        return $this->action === SubscriptionAction::DOWNGRADE;
    }

    public function isPlanChange(): bool
    {
        return in_array($this->action, [SubscriptionAction::UPGRADE, SubscriptionAction::DOWNGRADE]);
    }

    public function getActionLabel(): string
    {
        return $this->action->label();
    }
}
