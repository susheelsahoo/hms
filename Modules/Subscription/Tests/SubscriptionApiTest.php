<?php

namespace Modules\Subscription\Tests\Feature;

use Modules\Subscription\Tests\TestCase;
use Modules\Subscription\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Organization;

class SubscriptionApiTest extends TestCase
{
    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_can_list_subscription_plans()
    {
        SubscriptionPlan::factory()->count(3)->create(['is_active' => true]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/subscription-plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price_monthly',
                        'price_yearly',
                    ]
                ]
            ]);
    }

    public function test_can_get_plan_by_slug()
    {
        $plan = SubscriptionPlan::factory()->create(['slug' => 'professional']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/subscription-plans/slug/professional');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $plan->id);
    }

    public function test_can_compare_plans()
    {
        $plan1 = SubscriptionPlan::factory()->create();
        $plan2 = SubscriptionPlan::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/subscription-plans/compare', [
                'plan_ids' => [$plan1->id, $plan2->id],
            ]);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
