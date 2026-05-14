<?php

namespace Modules\GlobalAnalytics\Interfaces;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface GlobalAnalyticsRepositoryInterface
{
    public function dailySummary(Carbon $date): array;

    public function bookingSummary(Carbon $startDate, Carbon $endDate): array;

    public function revenueSummary(Carbon $startDate, Carbon $endDate): array;

    public function subscriptionSummary(Carbon $startDate, Carbon $endDate): array;

    public function systemHealthSummary(Carbon $startDate, Carbon $endDate): array;

    public function trend(string $modelClass, string $dateColumn, Carbon $startDate, Carbon $endDate): Collection;
}
