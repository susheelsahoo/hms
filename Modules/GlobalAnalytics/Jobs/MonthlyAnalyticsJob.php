<?php

namespace Modules\GlobalAnalytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\GlobalAnalytics\Services\GlobalAnalyticsAggregator;

class MonthlyAnalyticsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(GlobalAnalyticsAggregator $aggregator): void
    {
        $date = now()->startOfMonth();

        while ($date->lte(now())) {
            $aggregator->aggregateDaily($date->copy());
            $date->addDay();
        }
    }
}
