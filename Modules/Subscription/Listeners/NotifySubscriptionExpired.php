<?php

namespace Modules\Subscription\Listeners;

use Modules\Subscription\Events\SubscriptionExpired;
use Modules\Subscription\Jobs\SendSubscriptionExpiredNotification;

class NotifySubscriptionExpired
{
    public function handle(SubscriptionExpired $event): void
    {
        // Dispatch async job
        SendSubscriptionExpiredNotification::dispatch($event->subscription);
    }
}
