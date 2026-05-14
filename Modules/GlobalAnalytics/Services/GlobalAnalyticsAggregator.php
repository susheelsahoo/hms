<?php

namespace Modules\GlobalAnalytics\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\GlobalAnalytics\Models\AnalyticsDaily;
use Modules\GlobalAnalytics\Models\BookingStatistic;
use Modules\GlobalAnalytics\Models\RevenueStatistic;
use Modules\GlobalAnalytics\Models\SubscriptionStatistic;
use Modules\GlobalAnalytics\Models\SystemHealthStatistic;
use Modules\Subscription\Enums\SubscriptionAction;
use Modules\Subscription\Enums\SubscriptionStatus;

class GlobalAnalyticsAggregator
{
    public function aggregateDaily(?Carbon $date = null): void
    {
        $date ??= now();

        DB::transaction(function () use ($date): void {
            $this->aggregatePlatform($date);
            $this->aggregateBookings($date);
            $this->aggregateRevenue($date);
            $this->aggregateSubscriptions($date);
            $this->aggregateSystemHealth($date);
        });

        $this->clearDashboardCache();
    }

    public function aggregatePlatform(Carbon $date): AnalyticsDaily
    {
        $dateString = $date->toDateString();

        return AnalyticsDaily::query()->updateOrCreate(
            ['analytics_date' => $dateString],
            [
                'total_organizations' => $this->countTable('organizations'),
                'total_hotels' => $this->countTable('hotels'),
                'total_rooms' => $this->countTable('rooms'),
                'total_users' => $this->countTable('users'),
                'total_bookings' => $this->countTable('bookings'),
                'total_revenue' => $this->paidPaymentQuery()->sum('amount'),
                'active_subscriptions' => $this->subscriptionCount(SubscriptionStatus::ACTIVE->value),
                'trial_subscriptions' => $this->subscriptionCount(SubscriptionStatus::TRIAL->value),
                'cancellations' => $this->subscriptionCount(SubscriptionStatus::CANCELLED->value),
                'metadata' => [
                    'new_organizations' => $this->createdOnCount('organizations', $date),
                    'active_users' => $this->activeUsers($date),
                    'daily_active_organizations' => $this->dailyActiveOrganizations($date),
                    'monthly_active_organizations' => $this->monthlyActiveOrganizations($date),
                ],
            ]
        );
    }

    public function aggregateBookings(Carbon $date): BookingStatistic
    {
        $dateString = $date->toDateString();
        $query = DB::table('bookings')->whereDate('created_at', $dateString);
        $totalBookings = (clone $query)->count();
        $completedBookings = (clone $query)->whereIn('booking_status', ['confirmed', 'checked_in', 'checked_out'])->count();
        $cancelledBookings = (clone $query)->where('booking_status', 'cancelled')->count();
        $noShowBookings = (clone $query)->where('booking_status', 'no_show')->count();
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalRooms = max($this->countTable('rooms'), 1);
        $occupiedRooms = DB::table('rooms')->whereIn('status', ['occupied', 'reserved'])->count();

        return BookingStatistic::query()->updateOrCreate(
            [
                'organization_id' => null,
                'hotel_id' => null,
                'statistic_date' => $dateString,
            ],
            [
                'total_bookings' => $totalBookings,
                'completed_bookings' => $completedBookings,
                'cancelled_bookings' => $cancelledBookings,
                'no_show_bookings' => $noShowBookings,
                'total_revenue' => $totalRevenue,
                'average_booking_value' => $totalBookings > 0 ? round($totalRevenue / $totalBookings, 2) : 0,
                'occupancy_rate' => round(($occupiedRooms / $totalRooms) * 100, 2),
                'metadata' => [
                    'success_rate' => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 2) : 0,
                    'cancellation_rate' => $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 2) : 0,
                    'no_show_rate' => $totalBookings > 0 ? round(($noShowBookings / $totalBookings) * 100, 2) : 0,
                    'peak_booking_hours' => $this->peakBookingHours($date),
                    'booking_sources' => $this->bookingSources($date),
                    'most_active_hotels' => $this->mostActiveHotels($date),
                ],
            ]
        );
    }

    public function aggregateRevenue(Carbon $date): RevenueStatistic
    {
        $dateString = $date->toDateString();
        $monthStart = $date->copy()->startOfMonth();
        $yearStart = $date->copy()->startOfYear();

        return RevenueStatistic::query()->updateOrCreate(
            ['statistic_date' => $dateString],
            [
                'total_revenue' => $this->paidPaymentQuery()->whereDate('paid_at', $dateString)->sum('amount'),
                'monthly_revenue' => $this->paidPaymentQuery()->whereBetween('paid_at', [$monthStart, $date->copy()->endOfDay()])->sum('amount'),
                'annual_revenue' => $this->paidPaymentQuery()->whereBetween('paid_at', [$yearStart, $date->copy()->endOfDay()])->sum('amount'),
                'refunds' => DB::table('payments')->where('payment_status', 'refunded')->whereDate('updated_at', $dateString)->sum('amount'),
                'failed_payments' => DB::table('payments')->where('payment_status', 'failed')->whereDate('updated_at', $dateString)->count(),
                'pending_payments' => DB::table('payments')->where('payment_status', 'pending')->count(),
                'metadata' => [
                    'revenue_by_country' => $this->revenueByCountry($date),
                    'revenue_by_organization' => $this->revenueByOrganization($date),
                ],
            ]
        );
    }

    public function aggregateSubscriptions(Carbon $date): SubscriptionStatistic
    {
        $dateString = $date->toDateString();
        $active = $this->subscriptionCount(SubscriptionStatus::ACTIVE->value);
        $cancelled = $this->subscriptionCount(SubscriptionStatus::CANCELLED->value);
        $trial = $this->subscriptionCount(SubscriptionStatus::TRIAL->value);
        $trialConverted = DB::table('subscriptions')->where('status', SubscriptionStatus::ACTIVE->value)->whereNotNull('trial_ends_at')->count();
        $totalSubscriptions = max($this->countTable('subscriptions'), 1);

        return SubscriptionStatistic::query()->updateOrCreate(
            ['statistic_date' => $dateString],
            [
                'active_subscriptions' => $active,
                'expired_subscriptions' => $this->subscriptionCount(SubscriptionStatus::EXPIRED->value),
                'cancelled_subscriptions' => $cancelled,
                'upgrades' => $this->subscriptionHistoryCount(SubscriptionAction::UPGRADE->value, $date),
                'downgrades' => $this->subscriptionHistoryCount(SubscriptionAction::DOWNGRADE->value, $date),
                'churn_rate' => round(($cancelled / $totalSubscriptions) * 100, 2),
                'conversion_rate' => $trial > 0 ? round(($trialConverted / ($trial + $trialConverted)) * 100, 2) : 0,
                'metadata' => [
                    'most_popular_plans' => $this->mostPopularPlans(),
                    'trial_organizations' => $trial,
                ],
            ]
        );
    }

    public function aggregateSystemHealth(Carbon $date): SystemHealthStatistic
    {
        return SystemHealthStatistic::query()->updateOrCreate(
            ['statistic_date' => $date->toDateString()],
            [
                'api_response_time' => 0,
                'queue_jobs_pending' => $this->countTable('jobs'),
                'failed_jobs' => $this->countTable('failed_jobs'),
                'cache_hit_rate' => 0,
                'database_connections' => 1,
                'slow_queries' => 0,
                'error_rate' => 0,
                'metadata' => [
                    'queue_connection' => config('queue.default'),
                    'cache_store' => config('cache.default'),
                    'database_connection' => config('database.default'),
                ],
            ]
        );
    }

    private function paidPaymentQuery()
    {
        return DB::table('payments')->where('payment_status', 'paid');
    }

    private function countTable(string $table): int
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : 0;
    }

    private function createdOnCount(string $table, Carbon $date): int
    {
        return Schema::hasTable($table) ? DB::table($table)->whereDate('created_at', $date->toDateString())->count() : 0;
    }

    private function activeUsers(Carbon $date): int
    {
        return DB::table('users')
            ->where('status', 'active')
            ->where(function ($query) use ($date): void {
                $query->whereDate('last_login_at', $date->toDateString())
                    ->orWhereDate('updated_at', $date->toDateString());
            })
            ->count();
    }

    private function dailyActiveOrganizations(Carbon $date): int
    {
        return DB::table('bookings')
            ->whereDate('created_at', $date->toDateString())
            ->distinct('organization_id')
            ->count('organization_id');
    }

    private function monthlyActiveOrganizations(Carbon $date): int
    {
        return DB::table('bookings')
            ->whereBetween('created_at', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
            ->distinct('organization_id')
            ->count('organization_id');
    }

    private function subscriptionCount(string $status): int
    {
        return Schema::hasTable('subscriptions') ? DB::table('subscriptions')->where('status', $status)->count() : 0;
    }

    private function subscriptionHistoryCount(string $action, Carbon $date): int
    {
        return Schema::hasTable('subscription_histories')
            ? DB::table('subscription_histories')->where('action', $action)->whereDate('created_at', $date->toDateString())->count()
            : 0;
    }

    private function peakBookingHours(Carbon $date): array
    {
        $hourExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%H', created_at)"
            : 'EXTRACT(HOUR FROM created_at)';

        return DB::table('bookings')
            ->selectRaw("{$hourExpression} as hour, COUNT(*) as total")
            ->whereDate('created_at', $date->toDateString())
            ->groupByRaw($hourExpression)
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function bookingSources(Carbon $date): array
    {
        return DB::table('bookings')
            ->selectRaw('source, COUNT(*) as total')
            ->whereDate('created_at', $date->toDateString())
            ->groupBy('source')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    private function mostActiveHotels(Carbon $date): array
    {
        return DB::table('bookings')
            ->join('hotels', 'hotels.id', '=', 'bookings.hotel_id')
            ->selectRaw('hotels.id, hotels.name, COUNT(bookings.id) as total_bookings')
            ->whereDate('bookings.created_at', $date->toDateString())
            ->groupBy('hotels.id', 'hotels.name')
            ->orderByDesc('total_bookings')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function revenueByCountry(Carbon $date): array
    {
        return DB::table('payments')
            ->join('organizations', 'organizations.id', '=', 'payments.organization_id')
            ->selectRaw("COALESCE(organizations.country, 'NA') as country, SUM(payments.amount) as total")
            ->where('payments.payment_status', 'paid')
            ->whereDate('payments.paid_at', $date->toDateString())
            ->groupBy('organizations.country')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    private function revenueByOrganization(Carbon $date): array
    {
        return DB::table('payments')
            ->join('organizations', 'organizations.id', '=', 'payments.organization_id')
            ->selectRaw('organizations.id, organizations.name, SUM(payments.amount) as total')
            ->where('payments.payment_status', 'paid')
            ->whereDate('payments.paid_at', $date->toDateString())
            ->groupBy('organizations.id', 'organizations.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function mostPopularPlans(): array
    {
        return DB::table('subscriptions')
            ->join('subscription_plans', 'subscription_plans.id', '=', 'subscriptions.subscription_plan_id')
            ->selectRaw('subscription_plans.id, subscription_plans.name, COUNT(subscriptions.id) as total')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function clearDashboardCache(): void
    {
        Cache::forget('global_analytics.dashboard');
        Cache::forget('global_analytics.bookings');
        Cache::forget('global_analytics.revenue');
        Cache::forget('global_analytics.subscriptions');
        Cache::forget('global_analytics.system_health');
    }
}
