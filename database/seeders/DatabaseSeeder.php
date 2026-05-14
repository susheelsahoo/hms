<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Role\Models\Role;
use Modules\Subscription\Database\Seeders\SubscriptionPlanSeeder;
use Modules\User\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        $roles = Role::query()
            ->whereIn('slug', [
                Role::SUPER_ADMIN,
                Role::HOTEL_ADMIN,
                Role::HOTEL_MANAGER,
            ])
            ->pluck('id', 'slug');

        $users = [
            [
                'email' => 'admin@hms.test',
                'role_id' => $roles[Role::SUPER_ADMIN],
                'first_name' => 'Super',
                'last_name' => 'Admin',
            ],
            [
                'email' => 'hoteladmin@hms.test',
                'role_id' => $roles[Role::HOTEL_ADMIN],
                'first_name' => 'Hotel',
                'last_name' => 'Admin',
            ],
            [
                'email' => 'manager@hms.test',
                'role_id' => $roles[Role::HOTEL_MANAGER],
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

        // Seed dummy data for development/testing
        $this->call([
            DummyDataSeeder::class,
        ]);
    }
}
