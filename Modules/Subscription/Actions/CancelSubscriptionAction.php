<?php

namespace Modules\Subscription\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Enums\SubscriptionStatus;
use Modules\Subscription\Events\SubscriptionCancelled;

class CancelSubscriptionAction
{
    public function __invoke(Subscription $subscription, ?string $reason = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $reason) {
            $subscription->update([
                'status' => SubscriptionStatus::CANCELLED,
                'cancelled_at' => now(),
                'auto_renew' => false,
            ]);

            SubscriptionCancelled::dispatch($subscription, $reason);
            return $subscription;
        });
    }
}
