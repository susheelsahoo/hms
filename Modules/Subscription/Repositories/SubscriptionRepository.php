<?php

namespace Modules\Subscription\Repositories;

use Modules\Subscription\Interfaces\SubscriptionRepositoryInterface;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Enums\SubscriptionStatus;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function findById(int $id): ?Subscription
    {
        return Subscription::find($id);
    }

    public function findByOrganizationId(int $organizationId): ?Subscription
    {
        return Subscription::byOrganization($organizationId)->first();
    }

    public function create(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function update(Subscription $subscription, array $data): bool
    {
        return $subscription->update($data);
    }

    public function delete(Subscription $subscription): bool
    {
        return $subscription->delete();
    }

    public function getActive(): iterable
    {
        return Subscription::active()
            ->with('plan', 'organization')
            ->get();
    }

    public function getExpired(): iterable
    {
        return Subscription::where('status', SubscriptionStatus::EXPIRED)
            ->with('plan', 'organization')
            ->get();
    }

    public function getTrialEnding(): iterable
    {
        return Subscription::trial()
            ->where('trial_ends_at', '<=', now()->addDays(3))
            ->with('plan', 'organization')
            ->get();
    }

    public function getPendingRenewal(): iterable
    {
        return Subscription::autoRenewal()
            ->where('renewal_at', '<=', now())
            ->where('status', SubscriptionStatus::ACTIVE)
            ->with('plan', 'organization')
            ->get();
    }
}
