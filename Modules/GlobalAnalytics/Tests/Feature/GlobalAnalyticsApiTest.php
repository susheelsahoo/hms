<?php

namespace Modules\GlobalAnalytics\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Role\Models\Role;
use Tests\TestCase;

class GlobalAnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_global_analytics_dashboard(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::query()->where('slug', Role::SUPER_ADMIN)->value('id'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/global-analytics/dashboard');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'platform',
                    'bookings',
                    'revenue',
                    'subscriptions',
                    'system_health',
                    'widgets',
                    'charts',
                ],
            ]);
    }

    public function test_non_super_admin_cannot_view_global_analytics_dashboard(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::query()->where('slug', Role::HOTEL_ADMIN)->value('id'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/global-analytics/dashboard');

        $response->assertForbidden();
    }
}
