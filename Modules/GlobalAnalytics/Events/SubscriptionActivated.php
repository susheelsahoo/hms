<?php

namespace Modules\GlobalAnalytics\Events;

class SubscriptionActivated extends AnalyticsSignalReceived
{
    public function __construct(array $payload = [])
    {
        parent::__construct('subscription_activated', now(), $payload);
    }
}
