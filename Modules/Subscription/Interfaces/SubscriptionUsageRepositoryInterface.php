<?php

namespace Modules\Subscription\Interfaces;

use Modules\Subscription\Models\SubscriptionUsage;

interface SubscriptionUsageRepositoryInterface
{
    public function findById(int $id): ?SubscriptionUsage;
    public function findBySubscriptionId(int $subscriptionId): ?SubscriptionUsage;
    public function create(array $data): SubscriptionUsage;
    public function update(SubscriptionUsage $usage, array $data): bool;
}
