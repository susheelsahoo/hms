<?php

namespace Modules\Subscription\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Resources\SubscriptionPlanResource;

class SubscriptionPlanController
{
    /**
     * Get all active subscription plans
     */
    public function index(): JsonResponse
    {
        $plans = SubscriptionPlan::active()
            ->with('features')
            ->orderBy('price_monthly')
            ->get();

        return response()->json([
            'success' => true,
            'data' => SubscriptionPlanResource::collection($plans),
        ]);
    }

    /**
     * Get a specific subscription plan
     */
    public function show(int $id): JsonResponse
    {
        $plan = SubscriptionPlan::find($id);

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SubscriptionPlanResource($plan->load('features')),
        ]);
    }

    /**
     * Get plan by slug
     */
    public function bySlug(string $slug): JsonResponse
    {
        $plan = SubscriptionPlan::where('slug', $slug)->active()->first();

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SubscriptionPlanResource($plan->load('features')),
        ]);
    }

    /**
     * Compare plans
     */
    public function compare(Request $request): JsonResponse
    {
        $planIds = $request->input('plan_ids', []);
        $plans = SubscriptionPlan::active()
            ->whereIn('id', $planIds)
            ->with('features')
            ->get();

        return response()->json([
            'success' => true,
            'data' => SubscriptionPlanResource::collection($plans),
        ]);
    }
}
