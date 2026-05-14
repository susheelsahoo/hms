<?php

namespace Modules\Subscription\Listeners;

use Modules\Subscription\Events\SubscriptionCreated;
use Modules\Subscription\Jobs\SendSubscriptionWelcomeEmail;

class SendSubscriptionCreatedNotification
{
    public function handle(SubscriptionCreated $event): void
    {
        // Dispatch async job to send welcome email
        SendSubscriptionWelcomeEmail::dispatch($event->subscription);
    }
}
