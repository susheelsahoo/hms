<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Tests\TestCase;

class SuperAdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_login_and_reach_dashboard(): void
    {
        $role = Role::query()->where('slug', Role::SUPER_ADMIN)->firstOrFail();

        User::query()->create([
            'organization_id' => null,
            'role_id' => $role->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@hms.test',
            'password' => Hash::make('password'),
            'status' => 'active',
            'metadata' => [],
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@hms.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('super-admin.dashboard', absolute: false));
        $this->assertAuthenticated();

        $this->get(route('super-admin.dashboard'))->assertOk()->assertSee('Welcome, Super Admin');
    }
}
