<?php

namespace Modules\GlobalAnalytics\Listeners;

use Modules\GlobalAnalytics\Events\AnalyticsSignalReceived;
use Modules\GlobalAnalytics\Jobs\AnalyticsAggregationJob;

class QueueAnalyticsAggregation
{
    public function handle(AnalyticsSignalReceived $event): void
    {
        AnalyticsAggregationJob::dispatch($event->analyticsDate, [
            'signal' => $event->signal,
            'payload' => $event->payload,
        ]);
    }
}
