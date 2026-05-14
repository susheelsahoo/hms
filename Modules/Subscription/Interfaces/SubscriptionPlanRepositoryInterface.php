<?php

namespace Modules\Subscription\Interfaces;

use Modules\Subscription\Models\SubscriptionPlan;

interface SubscriptionPlanRepositoryInterface
{
    public function findById(int $id): ?SubscriptionPlan;
    public function findBySlug(string $slug): ?SubscriptionPlan;
    public function getActive(): iterable;
    public function getTrialPlan(): ?SubscriptionPlan;
    public function create(array $data): SubscriptionPlan;
    public function update(SubscriptionPlan $plan, array $data): bool;
    public function delete(SubscriptionPlan $plan): bool;
}
