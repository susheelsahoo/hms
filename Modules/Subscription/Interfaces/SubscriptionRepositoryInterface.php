<?php

namespace Modules\Subscription\Interfaces;

use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;

interface SubscriptionRepositoryInterface
{
    public function findById(int $id): ?Subscription;
    public function findByOrganizationId(int $organizationId): ?Subscription;
    public function create(array $data): Subscription;
    public function update(Subscription $subscription, array $data): bool;
    public function delete(Subscription $subscription): bool;
    public function getActive(): iterable;
    public function getExpired(): iterable;
    public function getTrialEnding(): iterable;
    public function getPendingRenewal(): iterable;
}
