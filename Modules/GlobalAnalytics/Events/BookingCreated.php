<?php

namespace Modules\GlobalAnalytics\Events;

class BookingCreated extends AnalyticsSignalReceived
{
    public function __construct(array $payload = [])
    {
        parent::__construct('booking_created', now(), $payload);
    }
}
