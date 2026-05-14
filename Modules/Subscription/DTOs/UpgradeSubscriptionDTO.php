<?php

namespace Modules\Subscription\DTOs;

use Modules\Subscription\Enums\BillingCycle;

class UpgradeSubscriptionDTO
{
    public function __construct(
        public readonly int $subscriptionId,
        public readonly int $newPlanId,
        public readonly BillingCycle $billingCycle,
        public readonly bool $prorate = true,
        public readonly ?array $metadata = null,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            subscriptionId: $data['subscription_id'],
            newPlanId: $data['new_plan_id'],
            billingCycle: BillingCycle::from($data['billing_cycle'] ?? 'monthly'),
            prorate: $data['prorate'] ?? true,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'subscription_id' => $this->subscriptionId,
            'new_plan_id' => $this->newPlanId,
            'billing_cycle' => $this->billingCycle->value,
            'prorate' => $this->prorate,
            'metadata' => $this->metadata,
        ];
    }
}
