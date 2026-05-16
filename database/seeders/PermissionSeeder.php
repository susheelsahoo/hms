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
        // Organization Management
        Permission::MANAGE_ORGANIZATIONS => 'Manage organizations',
        Permission::VIEW_ORGANIZATION_REPORTS => 'View organization reports',

        // Hotel Management
        Permission::MANAGE_HOTELS => 'Manage hotels',
        Permission::VIEW_HOTEL_DETAILS => 'View hotel details',

        // Room Management
        Permission::MANAGE_ROOMS => 'Manage rooms',
        Permission::MANAGE_ROOM_TYPES => 'Manage room types',
        Permission::MANAGE_RATE_TYPES => 'Manage rate types',

        // Booking Management
        Permission::MANAGE_BOOKINGS => 'Manage bookings',
        Permission::VIEW_BOOKING_DETAILS => 'View booking details',
        Permission::CANCEL_BOOKINGS => 'Cancel bookings',

        // Guest Management
        Permission::MANAGE_GUESTS => 'Manage guests',
        Permission::VIEW_GUEST_HISTORY => 'View guest history',

        // Payment Management
        Permission::MANAGE_PAYMENTS => 'Manage payments',
        Permission::VIEW_PAYMENT_REPORTS => 'View payment reports',
        Permission::PROCESS_REFUNDS => 'Process refunds',
        Permission::APPROVE_REFUNDS => 'Approve refunds',

        // Invoice Management
        Permission::MANAGE_INVOICES => 'Manage invoices',
        Permission::VIEW_INVOICES => 'View invoices',

        // Reporting & Analytics
        Permission::VIEW_REPORTS => 'View reports',
        Permission::VIEW_ANALYTICS => 'View analytics',
        Permission::EXPORT_REPORTS => 'Export reports',

        // Staff/User Management
        Permission::MANAGE_STAFF => 'Manage staff',
        Permission::MANAGE_USERS => 'Manage users',
        Permission::MANAGE_ROLES => 'Manage roles',

        // Audit & Compliance
        Permission::VIEW_AUDIT_LOGS => 'View audit logs',
        Permission::MANAGE_AUDIT_LOGS => 'Manage audit logs',

        // Notification Management
        Permission::MANAGE_NOTIFICATIONS => 'Manage notifications',

        // Subscription Management
        Permission::MANAGE_SUBSCRIPTIONS => 'Manage subscriptions',
        Permission::VIEW_SUBSCRIPTION_DETAILS => 'View subscription details',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private array $rolePermissions = [
        Role::SUPER_ADMIN => [
            // All permissions for super admin
            Permission::MANAGE_ORGANIZATIONS,
            Permission::VIEW_ORGANIZATION_REPORTS,
            Permission::MANAGE_HOTELS,
            Permission::VIEW_HOTEL_DETAILS,
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_ROOM_TYPES,
            Permission::MANAGE_RATE_TYPES,
            Permission::MANAGE_BOOKINGS,
            Permission::VIEW_BOOKING_DETAILS,
            Permission::CANCEL_BOOKINGS,
            Permission::MANAGE_GUESTS,
            Permission::VIEW_GUEST_HISTORY,
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_PAYMENT_REPORTS,
            Permission::PROCESS_REFUNDS,
            Permission::APPROVE_REFUNDS,
            Permission::MANAGE_INVOICES,
            Permission::VIEW_INVOICES,
            Permission::VIEW_REPORTS,
            Permission::VIEW_ANALYTICS,
            Permission::EXPORT_REPORTS,
            Permission::MANAGE_STAFF,
            Permission::MANAGE_USERS,
            Permission::MANAGE_ROLES,
            Permission::VIEW_AUDIT_LOGS,
            Permission::MANAGE_AUDIT_LOGS,
            Permission::MANAGE_NOTIFICATIONS,
            Permission::MANAGE_SUBSCRIPTIONS,
            Permission::VIEW_SUBSCRIPTION_DETAILS,
        ],
        Role::ORGANIZATION_OWNER => [
            // Organization-level permissions
            Permission::MANAGE_HOTELS,
            Permission::VIEW_HOTEL_DETAILS,
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_ROOM_TYPES,
            Permission::MANAGE_RATE_TYPES,
            Permission::MANAGE_BOOKINGS,
            Permission::VIEW_BOOKING_DETAILS,
            Permission::CANCEL_BOOKINGS,
            Permission::MANAGE_GUESTS,
            Permission::VIEW_GUEST_HISTORY,
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_PAYMENT_REPORTS,
            Permission::PROCESS_REFUNDS,
            Permission::APPROVE_REFUNDS,
            Permission::MANAGE_INVOICES,
            Permission::VIEW_INVOICES,
            Permission::VIEW_REPORTS,
            Permission::VIEW_ANALYTICS,
            Permission::EXPORT_REPORTS,
            Permission::MANAGE_STAFF,
            Permission::MANAGE_USERS,
            Permission::VIEW_AUDIT_LOGS,
            Permission::MANAGE_NOTIFICATIONS,
            Permission::VIEW_SUBSCRIPTION_DETAILS,
        ],
        Role::HOTEL_ADMIN => [
            // Hotel-level admin permissions
            Permission::MANAGE_HOTELS,
            Permission::VIEW_HOTEL_DETAILS,
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_ROOM_TYPES,
            Permission::MANAGE_RATE_TYPES,
            Permission::MANAGE_BOOKINGS,
            Permission::VIEW_BOOKING_DETAILS,
            Permission::CANCEL_BOOKINGS,
            Permission::MANAGE_GUESTS,
            Permission::VIEW_GUEST_HISTORY,
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_PAYMENT_REPORTS,
            Permission::PROCESS_REFUNDS,
            Permission::MANAGE_INVOICES,
            Permission::VIEW_INVOICES,
            Permission::VIEW_REPORTS,
            Permission::VIEW_ANALYTICS,
            Permission::EXPORT_REPORTS,
            Permission::MANAGE_STAFF,
            Permission::VIEW_AUDIT_LOGS,
            Permission::MANAGE_NOTIFICATIONS,
        ],
        Role::HOTEL_MANAGER => [
            // Hotel manager - operational permissions
            Permission::VIEW_HOTEL_DETAILS,
            Permission::MANAGE_ROOMS,
            Permission::MANAGE_ROOM_TYPES,
            Permission::MANAGE_RATE_TYPES,
            Permission::MANAGE_BOOKINGS,
            Permission::VIEW_BOOKING_DETAILS,
            Permission::CANCEL_BOOKINGS,
            Permission::MANAGE_GUESTS,
            Permission::VIEW_GUEST_HISTORY,
            Permission::VIEW_PAYMENT_REPORTS,
            Permission::VIEW_REPORTS,
            Permission::VIEW_ANALYTICS,
            Permission::MANAGE_STAFF,
            Permission::VIEW_AUDIT_LOGS,
        ],
        Role::STAFF => [
            // General staff
            Permission::VIEW_HOTEL_DETAILS,
            Permission::MANAGE_BOOKINGS,
            Permission::VIEW_BOOKING_DETAILS,
            Permission::MANAGE_GUESTS,
            Permission::VIEW_GUEST_HISTORY,
        ],
        Role::RECEPTIONIST => [
            // Front desk staff
            Permission::VIEW_HOTEL_DETAILS,
            Permission::MANAGE_BOOKINGS,
            Permission::VIEW_BOOKING_DETAILS,
            Permission::CANCEL_BOOKINGS,
            Permission::MANAGE_GUESTS,
            Permission::VIEW_GUEST_HISTORY,
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_INVOICES,
        ],
        Role::ACCOUNTANT => [
            // Finance role
            Permission::MANAGE_PAYMENTS,
            Permission::VIEW_PAYMENT_REPORTS,
            Permission::PROCESS_REFUNDS,
            Permission::APPROVE_REFUNDS,
            Permission::MANAGE_INVOICES,
            Permission::VIEW_INVOICES,
            Permission::VIEW_REPORTS,
            Permission::VIEW_ANALYTICS,
            Permission::EXPORT_REPORTS,
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
