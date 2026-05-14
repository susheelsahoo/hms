<?php

namespace Modules\GlobalAnalytics\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\GlobalAnalytics\Interfaces\GlobalAnalyticsRepositoryInterface;
use Modules\GlobalAnalytics\Models\AnalyticsDaily;
use Modules\GlobalAnalytics\Models\BookingStatistic;
use Modules\GlobalAnalytics\Models\RevenueStatistic;
use Modules\GlobalAnalytics\Models\SubscriptionStatistic;
use Modules\GlobalAnalytics\Models\SystemHealthStatistic;

class GlobalAnalyticsRepository implements GlobalAnalyticsRepositoryInterface
{
    public function dailySummary(Carbon $date): array
    {
        return AnalyticsDaily::query()
            ->whereDate('analytics_date', $date)
            ->first()
            ?->toArray() ?? [];
    }

    public function bookingSummary(Carbon $startDate, Carbon $endDate): array
    {
        return BookingStatistic::query()
            ->whereNull('organization_id')
            ->whereNull('hotel_id')
            ->whereBetween('statistic_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('COALESCE(SUM(total_bookings), 0) as total_bookings')
            ->selectRaw('COALESCE(SUM(completed_bookings), 0) as completed_bookings')
            ->selectRaw('COALESCE(SUM(cancelled_bookings), 0) as cancelled_bookings')
            ->selectRaw('COALESCE(SUM(no_show_bookings), 0) as no_show_bookings')
            ->selectRaw('COALESCE(SUM(total_revenue), 0) as total_revenue')
            ->selectRaw('COALESCE(AVG(average_booking_value), 0) as average_booking_value')
            ->selectRaw('COALESCE(AVG(occupancy_rate), 0) as occupancy_rate')
            ->first()
            ->toArray();
    }

    public function revenueSummary(Carbon $startDate, Carbon $endDate): array
    {
        return RevenueStatistic::query()
            ->whereBetween('statistic_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('COALESCE(SUM(total_revenue), 0) as total_revenue')
            ->selectRaw('COALESCE(SUM(monthly_revenue), 0) as monthly_revenue')
            ->selectRaw('COALESCE(SUM(annual_revenue), 0) as annual_revenue')
            ->selectRaw('COALESCE(SUM(refunds), 0) as refunds')
            ->selectRaw('COALESCE(SUM(failed_payments), 0) as failed_payments')
            ->selectRaw('COALESCE(SUM(pending_payments), 0) as pending_payments')
            ->first()
            ->toArray();
    }

    public function subscriptionSummary(Carbon $startDate, Carbon $endDate): array
    {
        return SubscriptionStatistic::query()
            ->whereBetween('statistic_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('COALESCE(MAX(active_subscriptions), 0) as active_subscriptions')
            ->selectRaw('COALESCE(MAX(expired_subscriptions), 0) as expired_subscriptions')
            ->selectRaw('COALESCE(MAX(cancelled_subscriptions), 0) as cancelled_subscriptions')
            ->selectRaw('COALESCE(SUM(upgrades), 0) as upgrades')
            ->selectRaw('COALESCE(SUM(downgrades), 0) as downgrades')
            ->selectRaw('COALESCE(AVG(churn_rate), 0) as churn_rate')
            ->selectRaw('COALESCE(AVG(conversion_rate), 0) as conversion_rate')
            ->first()
            ->toArray();
    }

    public function systemHealthSummary(Carbon $startDate, Carbon $endDate): array
    {
        return SystemHealthStatistic::query()
            ->whereBetween('statistic_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('COALESCE(AVG(api_response_time), 0) as api_response_time')
            ->selectRaw('COALESCE(MAX(queue_jobs_pending), 0) as queue_jobs_pending')
            ->selectRaw('COALESCE(MAX(failed_jobs), 0) as failed_jobs')
            ->selectRaw('COALESCE(AVG(cache_hit_rate), 0) as cache_hit_rate')
            ->selectRaw('COALESCE(AVG(database_connections), 0) as database_connections')
            ->selectRaw('COALESCE(SUM(slow_queries), 0) as slow_queries')
            ->selectRaw('COALESCE(AVG(error_rate), 0) as error_rate')
            ->first()
            ->toArray();
    }

    public function trend(string $modelClass, string $dateColumn, Carbon $startDate, Carbon $endDate): Collection
    {
        return $modelClass::query()
            ->whereBetween($dateColumn, [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy($dateColumn)
            ->get();
    }
}
