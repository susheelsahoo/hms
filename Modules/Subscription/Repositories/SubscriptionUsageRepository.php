<?php

namespace Modules\Subscription\Repositories;

use Modules\Subscription\Interfaces\SubscriptionUsageRepositoryInterface;
use Modules\Subscription\Models\SubscriptionUsage;

class SubscriptionUsageRepository implements SubscriptionUsageRepositoryInterface
{
    public function findById(int $id): ?SubscriptionUsage
    {
        return SubscriptionUsage::find($id);
    }

    public function findBySubscriptionId(int $subscriptionId): ?SubscriptionUsage
    {
        return SubscriptionUsage::where('subscription_id', $subscriptionId)->first();
    }

    public function create(array $data): SubscriptionUsage
    {
        return SubscriptionUsage::create($data);
    }

    public function update(SubscriptionUsage $usage, array $data): bool
    {
        return $usage->update($data);
    }
}
