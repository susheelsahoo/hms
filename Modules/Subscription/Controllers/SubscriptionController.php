<?php

namespace Modules\Subscription\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\DTOs\CreateSubscriptionDTO;
use Modules\Subscription\DTOs\UpgradeSubscriptionDTO;
use Modules\Subscription\Enums\BillingCycle;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Requests\CreateSubscriptionRequest;
use Modules\Subscription\Requests\UpgradeSubscriptionRequest;
use Modules\Subscription\Requests\CancelSubscriptionRequest;
use Modules\Subscription\Resources\SubscriptionResource;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\Services\LimitValidator;

class SubscriptionController
{
    public function __construct(
        private SubscriptionService $subscriptionService,
        private LimitValidator $limitValidator,
    ) {}

    /**
     * Get organization subscription
     */
    public function show(Request $request): JsonResponse
    {
        $subscription = Subscription::where('organization_id', $request->user()->organization_id)
            ->with('plan', 'usage')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    /**
     * Create new subscription
     */
    public function store(CreateSubscriptionRequest $request): JsonResponse
    {
        try {
            $dto = CreateSubscriptionDTO::from($request->validated());
            $subscription = $this->subscriptionService->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'data' => new SubscriptionResource($subscription),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Upgrade subscription
     */
    public function upgrade(UpgradeSubscriptionRequest $request): JsonResponse
    {
        try {
            $dto = UpgradeSubscriptionDTO::from(array_merge(
                $request->validated(),
                ['billing_cycle' => BillingCycle::from($request->input('billing_cycle'))]
            ));

            $subscription = $this->subscriptionService->upgrade(
                $dto,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription upgraded successfully',
                'data' => new SubscriptionResource($subscription),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Downgrade subscription
     */
    public function downgrade(UpgradeSubscriptionRequest $request): JsonResponse
    {
        try {
            $dto = UpgradeSubscriptionDTO::from(array_merge(
                $request->validated(),
                ['billing_cycle' => BillingCycle::from($request->input('billing_cycle'))]
            ));

            $subscription = $this->subscriptionService->downgrade(
                $dto,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription downgraded successfully',
                'data' => new SubscriptionResource($subscription),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(CancelSubscriptionRequest $request): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->cancel(
                $request->input('subscription_id'),
                $request->input('reason'),
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
                'data' => new SubscriptionResource($subscription),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Get usage statistics
     */
    public function usage(Request $request): JsonResponse
    {
        try {
            $stats = $this->limitValidator->getUsageStats($request->user()->organization_id);

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
