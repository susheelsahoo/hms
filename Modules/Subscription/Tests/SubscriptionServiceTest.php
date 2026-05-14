<?php

namespace Modules\Subscription\Tests\Feature;

use Modules\Subscription\Tests\TestCase;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\DTOs\CreateSubscriptionDTO;
use App\Models\User;
use App\Models\Organization;

class SubscriptionServiceTest extends TestCase
{
    private SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SubscriptionService::class);
    }

    public function test_can_create_subscription()
    {
        $organization = Organization::factory()->create();
        $plan = SubscriptionPlan::factory()->create();

        $dto = new CreateSubscriptionDTO(
            organizationId: $organization->id,
            subscriptionPlanId: $plan->id,
            billingCycle: 'monthly',
            autoRenew: true,
        );

        $subscription = $this->service->create($dto);

        $this->assertNotNull($subscription->id);
        $this->assertEquals($organization->id, $subscription->organization_id);
        $this->assertEquals($plan->id, $subscription->subscription_plan_id);
        $this->assertTrue($subscription->isActive());
    }

    public function test_cannot_create_subscription_for_organization_that_already_has_one()
    {
        $organization = Organization::factory()->create();
        $plan = SubscriptionPlan::factory()->create();

        // Create first subscription
        $dto = new CreateSubscriptionDTO(
            organizationId: $organization->id,
            subscriptionPlanId: $plan->id,
        );
        $this->service->create($dto);

        // Try to create another
        $this->expectException(\Exception::class);
        $this->service->create($dto);
    }

    public function test_can_upgrade_subscription()
    {
        $organization = Organization::factory()->create();
        $plan1 = SubscriptionPlan::factory()->create(['price_monthly' => 29.99]);
        $plan2 = SubscriptionPlan::factory()->create(['price_monthly' => 79.99]);

        $subscription = Subscription::factory()->create([
            'organization_id' => $organization->id,
            'subscription_plan_id' => $plan1->id,
        ]);

        $upgraded = $this->service->upgrade(
            new \Modules\Subscription\DTOs\UpgradeSubscriptionDTO(
                subscriptionId: $subscription->id,
                newPlanId: $plan2->id,
                billingCycle: \Modules\Subscription\Enums\BillingCycle::MONTHLY,
            )
        );

        $this->assertEquals($plan2->id, $upgraded->subscription_plan_id);
    }

    public function test_can_cancel_subscription()
    {
        $subscription = Subscription::factory()->create();

        $cancelled = $this->service->cancel($subscription->id, 'No longer needed');

        $this->assertTrue($cancelled->isCancelled());
        $this->assertNotNull($cancelled->cancelled_at);
    }

    public function test_can_check_feature_access()
    {
        $organization = Organization::factory()->create();
        $plan = SubscriptionPlan::factory()->create();

        $subscription = Subscription::factory()->create([
            'organization_id' => $organization->id,
            'subscription_plan_id' => $plan->id,
        ]);

        // Mock feature
        $hasAccess = $this->service->hasFeatureAccess($organization->id, 'advanced_reporting');

        $this->assertIsBool($hasAccess);
    }
}
