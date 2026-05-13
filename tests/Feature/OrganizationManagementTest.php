<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Tests\TestCase;

class OrganizationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_organization(): void
    {
        $role = Role::query()->where('slug', Role::SUPER_ADMIN)->firstOrFail();

        $user = User::query()->create([
            'organization_id' => null,
            'role_id' => $role->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@example.test',
            'password' => Hash::make('password'),
            'status' => 'active',
            'metadata' => [],
        ]);

        $response = $this->actingAs($user)->post(route('super-admin.organizations.store'), [
            'name' => 'Acme Hospitality',
            'slug' => '',
            'email' => 'ops@acme.test',
            'phone' => '+15551234567',
            'address' => '1 Market Street',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'US',
            'zip_code' => '94105',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('super-admin.organizations.index', absolute: false));

        $this->assertDatabaseHas('organizations', [
            'name' => 'Acme Hospitality',
            'slug' => 'acme-hospitality',
            'currency' => 'USD',
            'country' => 'US',
        ]);
    }
}
