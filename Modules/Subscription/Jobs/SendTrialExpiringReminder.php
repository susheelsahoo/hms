<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Subscription\Models\Subscription;

class SendTrialExpiringReminder implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $subscriptions = Subscription::trial()
            ->where('trial_ends_at', '<=', now()->addDays(3))
            ->where('trial_ends_at', '>', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                \Log::info('Trial expiring reminder sent', ['subscription_id' => $subscription->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to send trial expiring reminder', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
