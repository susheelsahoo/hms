<?php

namespace Modules\GlobalAnalytics\Services;

use Illuminate\Support\Facades\Cache;
use Modules\GlobalAnalytics\DTOs\AnalyticsDateRangeDTO;
use Modules\GlobalAnalytics\Interfaces\GlobalAnalyticsRepositoryInterface;
use Modules\GlobalAnalytics\Models\AnalyticsDaily;
use Modules\GlobalAnalytics\Models\BookingStatistic;
use Modules\GlobalAnalytics\Models\RevenueStatistic;
use Modules\GlobalAnalytics\Models\SubscriptionStatistic;

class GlobalAnalyticsService
{
    public function __construct(
        private readonly GlobalAnalyticsRepositoryInterface $repository,
        private readonly GlobalAnalyticsAggregator $aggregator,
    ) {}

    public function dashboard(AnalyticsDateRangeDTO $range): array
    {
        return Cache::remember('global_analytics.dashboard', now()->addMinutes(15), function () use ($range): array {
            $today = now();

            if (! AnalyticsDaily::query()->whereDate('analytics_date', $today)->exists()) {
                $this->aggregator->aggregateDaily($today);
            }

            return [
                'platform' => $this->repository->dailySummary($today),
                'bookings' => $this->repository->bookingSummary($range->startDate, $range->endDate),
                'revenue' => $this->repository->revenueSummary($range->startDate, $range->endDate),
                'subscriptions' => $this->repository->subscriptionSummary($range->startDate, $range->endDate),
                'system_health' => $this->repository->systemHealthSummary($range->startDate, $range->endDate),
                'widgets' => $this->widgets($range),
                'charts' => [
                    'revenue_trends' => $this->repository->trend(RevenueStatistic::class, 'statistic_date', $range->startDate, $range->endDate),
                    'booking_trends' => $this->repository->trend(BookingStatistic::class, 'statistic_date', $range->startDate, $range->endDate),
                    'subscription_growth' => $this->repository->trend(SubscriptionStatistic::class, 'statistic_date', $range->startDate, $range->endDate),
                ],
            ];
        });
    }

    public function bookings(AnalyticsDateRangeDTO $range): array
    {
        return Cache::remember('global_analytics.bookings', now()->addMinutes(15), fn (): array => [
            'summary' => $this->repository->bookingSummary($range->startDate, $range->endDate),
            'trend' => $this->repository->trend(BookingStatistic::class, 'statistic_date', $range->startDate, $range->endDate),
        ]);
    }

    public function revenue(AnalyticsDateRangeDTO $range): array
    {
        return Cache::remember('global_analytics.revenue', now()->addMinutes(15), fn (): array => [
            'summary' => $this->repository->revenueSummary($range->startDate, $range->endDate),
            'trend' => $this->repository->trend(RevenueStatistic::class, 'statistic_date', $range->startDate, $range->endDate),
        ]);
    }

    public function subscriptions(AnalyticsDateRangeDTO $range): array
    {
        return Cache::remember('global_analytics.subscriptions', now()->addMinutes(15), fn (): array => [
            'summary' => $this->repository->subscriptionSummary($range->startDate, $range->endDate),
            'trend' => $this->repository->trend(SubscriptionStatistic::class, 'statistic_date', $range->startDate, $range->endDate),
        ]);
    }

    public function systemHealth(AnalyticsDateRangeDTO $range): array
    {
        return Cache::remember('global_analytics.system_health', now()->addMinutes(5), fn (): array => [
            'summary' => $this->repository->systemHealthSummary($range->startDate, $range->endDate),
        ]);
    }

    private function widgets(AnalyticsDateRangeDTO $range): array
    {
        $revenue = $this->repository->revenueSummary($range->startDate, $range->endDate);
        $bookings = $this->repository->bookingSummary($range->startDate, $range->endDate);
        $subscriptions = $this->repository->subscriptionSummary($range->startDate, $range->endDate);
        $health = $this->repository->systemHealthSummary($range->startDate, $range->endDate);

        return [
            'total_revenue' => (float) ($revenue['total_revenue'] ?? 0),
            'total_bookings' => (int) ($bookings['total_bookings'] ?? 0),
            'active_organizations' => (int) data_get($this->repository->dailySummary(now()), 'metadata.daily_active_organizations', 0),
            'mrr' => (float) ($revenue['monthly_revenue'] ?? 0),
            'arr' => (float) ($revenue['annual_revenue'] ?? 0),
            'churn_rate' => (float) ($subscriptions['churn_rate'] ?? 0),
            'occupancy_rate' => (float) ($bookings['occupancy_rate'] ?? 0),
            'platform_health' => (float) (100 - (float) ($health['error_rate'] ?? 0)),
            'queue_status' => (int) ($health['queue_jobs_pending'] ?? 0),
            'subscription_trends' => [
                'active' => (int) ($subscriptions['active_subscriptions'] ?? 0),
                'expired' => (int) ($subscriptions['expired_subscriptions'] ?? 0),
                'cancelled' => (int) ($subscriptions['cancelled_subscriptions'] ?? 0),
            ],
        ];
    }
}
