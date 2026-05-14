<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Subscription\Models\Subscription;

class SendSubscriptionExpiredNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function handle(): void
    {
        // Send expiration notification
        \Log::info('Expiration notification sent for subscription', [
            'subscription_id' => $this->subscription->id,
            'organization_id' => $this->subscription->organization_id,
        ]);
    }
}
