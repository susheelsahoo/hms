<?php

namespace Modules\Subscription\Listeners;

use Modules\Subscription\Events\SubscriptionCancelled;

class LogSubscriptionCancellation
{
    public function handle(SubscriptionCancelled $event): void
    {
        \Log::info('Subscription cancelled', [
            'subscription_id' => $event->subscription->id,
            'organization_id' => $event->subscription->organization_id,
            'reason' => $event->reason,
            'cancelled_at' => $event->subscription->cancelled_at,
        ]);
    }
}
