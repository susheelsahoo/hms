<?php

namespace Modules\Subscription\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Enums\SubscriptionStatus;
use Modules\Subscription\Events\SubscriptionCreated;

class CreateSubscriptionAction
{
    public function __invoke(array $data): Subscription
    {
        return DB::transaction(function () use ($data) {
            $subscription = Subscription::create($data);
            SubscriptionCreated::dispatch($subscription);
            return $subscription;
        });
    }
}
