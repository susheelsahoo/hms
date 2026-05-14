<?php

namespace Modules\GlobalAnalytics\Events;

class BookingCancelled extends AnalyticsSignalReceived
{
    public function __construct(array $payload = [])
    {
        parent::__construct('booking_cancelled', now(), $payload);
    }
}
