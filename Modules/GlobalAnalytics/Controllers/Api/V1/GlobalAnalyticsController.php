<?php

namespace Modules\GlobalAnalytics\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Modules\GlobalAnalytics\DTOs\AnalyticsDateRangeDTO;
use Modules\GlobalAnalytics\Requests\AnalyticsRangeRequest;
use Modules\GlobalAnalytics\Resources\GlobalAnalyticsResource;
use Modules\GlobalAnalytics\Services\GlobalAnalyticsService;

class GlobalAnalyticsController
{
    public function __construct(
        private readonly GlobalAnalyticsService $analyticsService,
    ) {}

    public function dashboard(AnalyticsRangeRequest $request): JsonResponse
    {
        return $this->analyticsResponse(
            $this->analyticsService->dashboard(AnalyticsDateRangeDTO::fromRequest($request->validated()))
        );
    }

    public function bookings(AnalyticsRangeRequest $request): JsonResponse
    {
        return $this->analyticsResponse(
            $this->analyticsService->bookings(AnalyticsDateRangeDTO::fromRequest($request->validated()))
        );
    }

    public function revenue(AnalyticsRangeRequest $request): JsonResponse
    {
        return $this->analyticsResponse(
            $this->analyticsService->revenue(AnalyticsDateRangeDTO::fromRequest($request->validated()))
        );
    }

    public function subscriptions(AnalyticsRangeRequest $request): JsonResponse
    {
        return $this->analyticsResponse(
            $this->analyticsService->subscriptions(AnalyticsDateRangeDTO::fromRequest($request->validated()))
        );
    }

    public function systemHealth(AnalyticsRangeRequest $request): JsonResponse
    {
        return $this->analyticsResponse(
            $this->analyticsService->systemHealth(AnalyticsDateRangeDTO::fromRequest($request->validated()))
        );
    }

    private function analyticsResponse(array $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new GlobalAnalyticsResource($data),
        ]);
    }
}
