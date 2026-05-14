<?php

namespace Modules\Subscription\Repositories;

use Modules\Subscription\Interfaces\SubscriptionPlanRepositoryInterface;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanRepository implements SubscriptionPlanRepositoryInterface
{
    public function findById(int $id): ?SubscriptionPlan
    {
        return SubscriptionPlan::find($id);
    }

    public function findBySlug(string $slug): ?SubscriptionPlan
    {
        return SubscriptionPlan::bySlug($slug)->first();
    }

    public function getActive(): iterable
    {
        return SubscriptionPlan::active()
            ->with('features')
            ->orderBy('price_monthly')
            ->get();
    }

    public function getTrialPlan(): ?SubscriptionPlan
    {
        return SubscriptionPlan::active()
            ->trialPlans()
            ->first();
    }

    public function create(array $data): SubscriptionPlan
    {
        return SubscriptionPlan::create($data);
    }

    public function update(SubscriptionPlan $plan, array $data): bool
    {
        return $plan->update($data);
    }

    public function delete(SubscriptionPlan $plan): bool
    {
        return $plan->delete();
    }
}
