<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Role\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * @var array<string, array{name: string, description: string}>
     */
    private array $roles = [
        Role::SUPER_ADMIN => [
            'name' => 'Super Admin',
            'description' => 'Platform administrator with access to all organizations and hotels.',
        ],
        Role::ORGANIZATION_OWNER => [
            'name' => 'Organization Owner',
            'description' => 'Tenant owner responsible for organization-level administration.',
        ],
        Role::HOTEL_ADMIN => [
            'name' => 'Hotel Admin',
            'description' => 'Hotel administrator with access to assigned hotels.',
        ],
        Role::HOTEL_MANAGER => [
            'name' => 'Hotel Manager',
            'description' => 'Operational manager for assigned hotel workflows.',
        ],
        Role::STAFF => [
            'name' => 'Staff',
            'description' => 'General staff member with limited operational access.',
        ],
        Role::RECEPTIONIST => [
            'name' => 'Receptionist',
            'description' => 'Front desk user for guest and booking operations.',
        ],
        Role::ACCOUNTANT => [
            'name' => 'Accountant',
            'description' => 'Finance user for payments, invoices, refunds, and reports.',
        ],
    ];

    public function run(): void
    {
        foreach ($this->roles as $slug => $role) {
            Role::query()->updateOrCreate(
                ['slug' => $slug],
                $role
            );
        }
    }
}
