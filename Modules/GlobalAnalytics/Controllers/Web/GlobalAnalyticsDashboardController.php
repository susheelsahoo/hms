<?php

namespace Modules\GlobalAnalytics\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GlobalAnalytics\DTOs\AnalyticsDateRangeDTO;
use Modules\GlobalAnalytics\Services\GlobalAnalyticsService;

class GlobalAnalyticsDashboardController
{
    public function __construct(
        private readonly GlobalAnalyticsService $analyticsService,
    ) {}

    public function __invoke(Request $request): View
    {
        $range = AnalyticsDateRangeDTO::fromRequest($request->only(['start_date', 'end_date']));

        return view('global-analytics::dashboard.index', [
            'analytics' => $this->analyticsService->dashboard($range),
        ]);
    }
}
