<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Subscription\Models\Subscription;

class SendSubscriptionWelcomeEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function handle(): void
    {
        // Send welcome email
        // Example: Mail::send(new SubscriptionWelcomeEmail($this->subscription));
        
        \Log::info('Welcome email sent for subscription', [
            'subscription_id' => $this->subscription->id,
            'organization_id' => $this->subscription->organization_id,
        ]);
    }
}
