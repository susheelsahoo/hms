<?php

namespace Modules\Subscription\Listeners;

use Modules\Subscription\Events\PlanUpgraded;

class LogPlanUpgrade
{
    public function handle(PlanUpgraded $event): void
    {
        \Log::info('Plan upgraded', [
            'subscription_id' => $event->subscription->id,
            'organization_id' => $event->subscription->organization_id,
            'old_plan_id' => $event->oldPlanId,
            'new_plan_id' => $event->newPlanId,
        ]);
    }
}
