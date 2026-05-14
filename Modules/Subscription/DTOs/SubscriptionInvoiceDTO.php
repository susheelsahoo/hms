<?php

namespace Modules\Subscription\DTOs;

class SubscriptionInvoiceDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $subscriptionId,
        public readonly float $amount,
        public readonly float $taxAmount = 0,
        public readonly string $currency = 'USD',
        public readonly ?string $paymentMethod = null,
        public readonly ?array $metadata = null,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            subscriptionId: $data['subscription_id'],
            amount: $data['amount'],
            taxAmount: $data['tax_amount'] ?? 0,
            currency: $data['currency'] ?? 'USD',
            paymentMethod: $data['payment_method'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'organization_id' => $this->organizationId,
            'subscription_id' => $this->subscriptionId,
            'amount' => $this->amount,
            'tax_amount' => $this->taxAmount,
            'total_amount' => $this->amount + $this->taxAmount,
            'currency' => $this->currency,
            'payment_method' => $this->paymentMethod,
            'metadata' => $this->metadata,
        ];
    }

    public function getTotalAmount(): float
    {
        return $this->amount + $this->taxAmount;
    }
}
