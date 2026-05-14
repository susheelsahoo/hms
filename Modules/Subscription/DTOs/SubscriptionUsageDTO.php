<?php

namespace Modules\Subscription\DTOs;

class SubscriptionUsageDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $subscriptionId,
        public readonly int $hotelsUsed,
        public readonly int $staffUsed,
        public readonly int $roomsUsed,
        public readonly int $bookingsUsed,
        public readonly int $storageUsed,
        public readonly ?array $metadata = null,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            subscriptionId: $data['subscription_id'],
            hotelsUsed: $data['hotels_used'] ?? 0,
            staffUsed: $data['staff_used'] ?? 0,
            roomsUsed: $data['rooms_used'] ?? 0,
            bookingsUsed: $data['bookings_used'] ?? 0,
            storageUsed: $data['storage_used'] ?? 0,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'organization_id' => $this->organizationId,
            'subscription_id' => $this->subscriptionId,
            'hotels_used' => $this->hotelsUsed,
            'staff_used' => $this->staffUsed,
            'rooms_used' => $this->roomsUsed,
            'bookings_used' => $this->bookingsUsed,
            'storage_used' => $this->storageUsed,
            'metadata' => $this->metadata,
        ];
    }
}
