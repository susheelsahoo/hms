<?php

namespace Modules\Subscription\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Enums\SubscriptionStatus;
use Modules\Subscription\Events\SubscriptionExpired;

class ExpireSubscriptionAction
{
    public function __invoke(Subscription $subscription): Subscription
    {
        return DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => SubscriptionStatus::EXPIRED,
                'ends_at' => now(),
            ]);

            SubscriptionExpired::dispatch($subscription);
            return $subscription;
        });
    }
}
