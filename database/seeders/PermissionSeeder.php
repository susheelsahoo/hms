<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Role\Models\Role;
use Modules\User\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private array $permissions = [
        Permission::MANAGE_HOTELS => 'Manage hotels',
        Permission::MANAGE_ROOMS => 'Manage rooms',
        Permission::MANAGE_BOOKINGS => 'Manage bookings',
        Permission::MANAGE_PAYMENTS => 'Manage payments',
        Permission::VIEW_REPORTS => 'View reports',
        Permission::MANAGE_STAFF => 'Manage staff',
        Permission::APPROVE_REFUNDS => 'Approve refunds',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private array $rolePermissions = [
        Role::SUPER_ADMIN => [
            Permission::MANAGE_HOTELS,
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_BOOKINGS,
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_REPORTS,
            Permission::MANAGE_STAFF,
            Permission::APPROVE_REFUNDS,
        ],
        Role::ORGANIZATION_OWNER => [
            Permission::MANAGE_HOTELS,
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_BOOKINGS,
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_REPORTS,
            Permission::MANAGE_STAFF,
            Permission::APPROVE_REFUNDS,
        ],
        Role::HOTEL_ADMIN => [
            Permission::MANAGE_HOTELS,
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_BOOKINGS,
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_REPORTS,
            Permission::MANAGE_STAFF,
            Permission::APPROVE_REFUNDS,
        ],
        Role::HOTEL_MANAGER => [
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_BOOKINGS,
            Permission::VIEW_REPORTS,
            Permission::MANAGE_STAFF,
        ],
        Role::STAFF => [
            Permission::MANAGE_BOOKINGS,
        ],
        Role::RECEPTIONIST => [
            Permission::MANAGE_BOOKINGS,
            Permission::MANAGE_PAYMENTS,
        ],
        Role::ACCOUNTANT => [
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_REPORTS,
            Permission::APPROVE_REFUNDS,
        ],
    ];

    public function run(): void
    {
        $permissionsBySlug = [];

        foreach ($this->permissions as $slug => $name) {
            $permissionsBySlug[$slug] = Permission::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => "{$name} permission"]
            );
        }

        Role::query()
            ->whereIn('slug', array_keys($this->rolePermissions))
            ->get()
            ->each(function (Role $role) use ($permissionsBySlug): void {
                $permissionIds = collect($this->rolePermissions[$role->slug] ?? [])
                    ->map(fn (string $slug): int => $permissionsBySlug[$slug]->id)
                    ->all();

                $role->permissions()->sync($permissionIds);
            });
    }
}
