<?php

namespace Modules\Subscription\DTOs;

class CreateSubscriptionDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $subscriptionPlanId,
        public readonly string $billingCycle = 'monthly',
        public readonly bool $autoRenew = true,
        public readonly ?array $metadata = null,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            subscriptionPlanId: $data['subscription_plan_id'],
            billingCycle: $data['billing_cycle'] ?? 'monthly',
            autoRenew: $data['auto_renew'] ?? true,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'organization_id' => $this->organizationId,
            'subscription_plan_id' => $this->subscriptionPlanId,
            'billing_cycle' => $this->billingCycle,
            'auto_renew' => $this->autoRenew,
            'metadata' => $this->metadata,
        ];
    }
}
