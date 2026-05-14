<?php

namespace Modules\GlobalAnalytics\Events;

class OrganizationCreated extends AnalyticsSignalReceived
{
    public function __construct(array $payload = [])
    {
        parent::__construct('organization_created', now(), $payload);
    }
}
