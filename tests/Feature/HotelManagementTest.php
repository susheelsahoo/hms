<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Organization\Models\Organization;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Tests\TestCase;

class HotelManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_hotel_under_organization(): void
    {
        $role = Role::query()->where('slug', Role::SUPER_ADMIN)->firstOrFail();

        $user = User::query()->create([
            'organization_id' => null,
            'role_id' => $role->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'hotel-owner@example.test',
            'password' => Hash::make('password'),
            'status' => 'active',
            'metadata' => [],
        ]);

        $organization = Organization::query()->create([
            'name' => 'Acme Hospitality',
            'slug' => 'acme-hospitality',
            'country' => 'US',
            'timezone' => 'America/New_York',
            'currency' => 'USD',
            'status' => 'active',
            'metadata' => [],
        ]);

        $response = $this->actingAs($user)->post(route('super-admin.organizations.hotels.store', $organization), [
            'name' => 'Acme Downtown',
            'slug' => '',
            'email' => 'downtown@acme.test',
            'phone' => '+15550000000',
            'description' => 'Central business hotel.',
            'address' => '10 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'US',
            'zip_code' => '10001',
            'timezone' => 'America/New_York',
            'currency' => 'USD',
            'checkin_time' => '14:00',
            'checkout_time' => '11:00',
            'star_rating' => 4,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('super-admin.organizations.hotels.index', $organization, absolute: false));

        $this->assertDatabaseHas('hotels', [
            'organization_id' => $organization->id,
            'name' => 'Acme Downtown',
            'slug' => 'acme-downtown',
            'currency' => 'USD',
            'timezone' => 'America/New_York',
        ]);
    }
}
