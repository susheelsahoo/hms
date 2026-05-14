<?php

namespace Modules\Subscription\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),
            'status' => $this->status->value,
            'billing_cycle' => $this->billing_cycle,
            'starts_at' => $this->starts_at->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'trial_ends_at' => $this->trial_ends_at?->toIso8601String(),
            'grace_ends_at' => $this->grace_ends_at?->toIso8601String(),
            'renewal_at' => $this->renewal_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'auto_renew' => $this->auto_renew,
            'days_remaining' => $this->daysUntilExpiration(),
            'days_trial_remaining' => $this->daysRemainingInTrial(),
            'is_trial_active' => $this->isTrialActive(),
            'is_active' => $this->isActive(),
            'can_upgrade' => $this->canUpgrade(),
            'can_downgrade' => $this->canDowngrade(),
            'can_cancel' => $this->canCancel(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
