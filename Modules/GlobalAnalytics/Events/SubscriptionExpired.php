<?php

namespace Modules\GlobalAnalytics\Events;

class SubscriptionExpired extends AnalyticsSignalReceived
{
    public function __construct(array $payload = [])
    {
        parent::__construct('subscription_expired', now(), $payload);
    }
}
