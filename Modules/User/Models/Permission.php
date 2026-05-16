<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Role\Models\Role;

class Permission extends Model
{
    // Organization Management
    public const MANAGE_ORGANIZATIONS = 'manage_organizations';
    public const VIEW_ORGANIZATION_REPORTS = 'view_organization_reports';

    // Hotel Management
    public const MANAGE_HOTELS = 'manage_hotels';
    public const VIEW_HOTEL_DETAILS = 'view_hotel_details';

    // Room Management
    public const MANAGE_ROOMS = 'manage_rooms';
    public const MANAGE_ROOM_TYPES = 'manage_room_types';
    public const MANAGE_RATE_TYPES = 'manage_rate_types';

    // Booking Management
    public const MANAGE_BOOKINGS = 'manage_bookings';
    public const VIEW_BOOKING_DETAILS = 'view_booking_details';
    public const CANCEL_BOOKINGS = 'cancel_bookings';

    // Guest Management
    public const MANAGE_GUESTS = 'manage_guests';
    public const VIEW_GUEST_HISTORY = 'view_guest_history';

    // Payment Management
    public const MANAGE_PAYMENTS = 'manage_payments';
    public const VIEW_PAYMENT_REPORTS = 'view_payment_reports';
    public const PROCESS_REFUNDS = 'process_refunds';
    public const APPROVE_REFUNDS = 'approve_refunds';

    // Invoice Management
    public const MANAGE_INVOICES = 'manage_invoices';
    public const VIEW_INVOICES = 'view_invoices';

    // Reporting & Analytics
    public const VIEW_REPORTS = 'view_reports';
    public const VIEW_ANALYTICS = 'view_analytics';
    public const EXPORT_REPORTS = 'export_reports';

    // Staff/User Management
    public const MANAGE_STAFF = 'manage_staff';
    public const MANAGE_USERS = 'manage_users';
    public const MANAGE_ROLES = 'manage_roles';

    // Audit & Compliance
    public const VIEW_AUDIT_LOGS = 'view_audit_logs';
    public const MANAGE_AUDIT_LOGS = 'manage_audit_logs';

    // Notification Management
    public const MANAGE_NOTIFICATIONS = 'manage_notifications';

    // Subscription Management
    public const MANAGE_SUBSCRIPTIONS = 'manage_subscriptions';
    public const VIEW_SUBSCRIPTION_DETAILS = 'view_subscription_details';

    protected $guarded = ['id'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')
            ->withTimestamps();
    }
}
