<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Subscription\Services\SubscriptionService;

class ProcessSubscriptionRenewals implements ShouldQueue
{
    use Queueable;

    public function handle(SubscriptionService $subscriptionService): void
    {
        $subscriptions = app('Modules\Subscription\Repositories\SubscriptionRepository')
            ->getPendingRenewal();

        foreach ($subscriptions as $subscription) {
            try {
                $subscriptionService->renew($subscription->id);
                \Log::info('Subscription renewed', ['subscription_id' => $subscription->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to renew subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
