<?php

namespace Modules\GlobalAnalytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\GlobalAnalytics\Models\AnalyticsDaily;
use Modules\GlobalAnalytics\Models\BookingStatistic;
use Modules\GlobalAnalytics\Models\RevenueStatistic;
use Modules\GlobalAnalytics\Models\SubscriptionStatistic;
use Modules\GlobalAnalytics\Models\SystemHealthStatistic;

class CleanupOldAnalyticsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $retentionDays = 730,
    ) {}

    public function handle(): void
    {
        $cutoff = now()->subDays($this->retentionDays)->toDateString();

        AnalyticsDaily::query()->whereDate('analytics_date', '<', $cutoff)->delete();
        BookingStatistic::query()->whereDate('statistic_date', '<', $cutoff)->delete();
        RevenueStatistic::query()->whereDate('statistic_date', '<', $cutoff)->delete();
        SubscriptionStatistic::query()->whereDate('statistic_date', '<', $cutoff)->delete();
        SystemHealthStatistic::query()->whereDate('statistic_date', '<', $cutoff)->delete();
    }
}
