<?php

namespace Modules\GlobalAnalytics\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GlobalAnalytics\Models\AnalyticsDaily;
use Modules\GlobalAnalytics\Models\BookingStatistic;
use Modules\GlobalAnalytics\Models\RevenueStatistic;
use Modules\GlobalAnalytics\Models\SubscriptionStatistic;
use Modules\GlobalAnalytics\Models\SystemHealthStatistic;
use Modules\GlobalAnalytics\Services\GlobalAnalyticsAggregator;
use Tests\TestCase;

class GlobalAnalyticsAggregatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_aggregation_creates_all_global_snapshots(): void
    {
        app(GlobalAnalyticsAggregator::class)->aggregateDaily(now());

        $this->assertSame(1, AnalyticsDaily::query()->count());
        $this->assertSame(1, BookingStatistic::query()->count());
        $this->assertSame(1, RevenueStatistic::query()->count());
        $this->assertSame(1, SubscriptionStatistic::query()->count());
        $this->assertSame(1, SystemHealthStatistic::query()->count());
    }
}
