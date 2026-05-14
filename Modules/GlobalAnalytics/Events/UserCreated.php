<?php

namespace Modules\GlobalAnalytics\Events;

class UserCreated extends AnalyticsSignalReceived
{
    public function __construct(array $payload = [])
    {
        parent::__construct('user_created', now(), $payload);
    }
}
