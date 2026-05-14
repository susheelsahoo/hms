<?php

namespace Modules\GlobalAnalytics\DTOs;

use Illuminate\Support\Carbon;

class AnalyticsDateRangeDTO
{
    public function __construct(
        public readonly Carbon $startDate,
        public readonly Carbon $endDate,
    ) {}

    public static function fromRequest(array $data): self
    {
        $endDate = isset($data['end_date'])
            ? Carbon::parse($data['end_date'])->endOfDay()
            : now()->endOfDay();

        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])->startOfDay()
            : $endDate->copy()->subDays(29)->startOfDay();

        return new self($startDate, $endDate);
    }
}
