<?php

namespace Modules\GlobalAnalytics\Events;

class PaymentCompleted extends AnalyticsSignalReceived
{
    public function __construct(array $payload = [])
    {
        parent::__construct('payment_completed', now(), $payload);
    }
}
