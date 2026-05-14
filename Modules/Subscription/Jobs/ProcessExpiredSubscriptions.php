<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Subscription\Services\SubscriptionService;

class ProcessExpiredSubscriptions implements ShouldQueue
{
    use Queueable;

    public function handle(SubscriptionService $subscriptionService): void
    {
        $subscriptions = app('Modules\Subscription\Repositories\SubscriptionRepository')
            ->getExpired();

        foreach ($subscriptions as $subscription) {
            try {
                \Log::info('Processing expired subscription', ['subscription_id' => $subscription->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to process expired subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
