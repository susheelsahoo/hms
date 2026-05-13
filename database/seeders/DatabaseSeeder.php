<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Role\Models\Role;
use Modules\User\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            Role::SUPER_ADMIN => 'Super Admin',
            Role::HOTEL_ADMIN => 'Hotel Admin',
            Role::HOTEL_MANAGER => 'Hotel Manager',
        ];

        $roleIds = [];

        foreach ($roles as $slug => $name) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => "{$name} role"]
            );

            $roleIds[$slug] = $role->id;
        }

        $users = [
            [
                'email' => 'admin@hms.test',
                'role_id' => $roleIds[Role::SUPER_ADMIN],
                'first_name' => 'Super',
                'last_name' => 'Admin',
            ],
            [
                'email' => 'hoteladmin@hms.test',
                'role_id' => $roleIds[Role::HOTEL_ADMIN],
                'first_name' => 'Hotel',
                'last_name' => 'Admin',
            ],
            [
                'email' => 'manager@hms.test',
                'role_id' => $roleIds[Role::HOTEL_MANAGER],
                'first_name' => 'Hotel',
                'last_name' => 'Manager',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'organization_id' => null,
                    'role_id' => $user['role_id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'phone' => null,
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'metadata' => [],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
