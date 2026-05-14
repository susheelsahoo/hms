# Subscription Module - Quick Reference

## Directory Structure

```
Modules/Subscription/
├── config/
│   └── subscription.php              # Configuration file
├── Database/
│   ├── Migrations/                   # 6 migration files
│   └── Seeders/
│       └── SubscriptionPlanSeeder.php
├── Enums/                            # 4 enum classes
│   ├── SubscriptionStatus.php
│   ├── BillingCycle.php
│   ├── SubscriptionAction.php
│   └── InvoiceStatus.php
├── Models/                           # 6 model classes
│   ├── Subscription.php
│   ├── SubscriptionPlan.php
│   ├── SubscriptionUsage.php
│   ├── SubscriptionInvoice.php
│   ├── SubscriptionHistory.php
│   └── SubscriptionFeature.php
├── DTOs/                             # 4 data transfer objects
│   ├── CreateSubscriptionDTO.php
│   ├── UpgradeSubscriptionDTO.php
│   ├── SubscriptionUsageDTO.php
│   └── SubscriptionInvoiceDTO.php
├── Exceptions/
│   └── SubscriptionException.php
├── Repositories/                     # 3 repositories + interfaces
│   ├── Contracts/
│   ├── SubscriptionRepository.php
│   ├── SubscriptionPlanRepository.php
│   └── SubscriptionUsageRepository.php
├── Services/                         # 3 core services
│   ├── SubscriptionService.php       # 13 public methods
│   ├── InvoiceService.php
│   └── LimitValidator.php
├── Events/                           # 7 events
│   ├── SubscriptionCreated.php
│   ├── SubscriptionActivated.php
│   ├── SubscriptionExpired.php
│   ├── SubscriptionCancelled.php
│   ├── PlanUpgraded.php
│   ├── PlanDowngraded.php
│   └── InvoiceGenerated.php
├── Listeners/                        # 5 listeners
│   ├── SendSubscriptionCreatedNotification.php
│   ├── NotifySubscriptionExpired.php
│   ├── SendInvoiceNotification.php
│   ├── LogPlanUpgrade.php
│   └── LogSubscriptionCancellation.php
├── Jobs/                             # 6 queue jobs
│   ├── SendSubscriptionWelcomeEmail.php
│   ├── SendSubscriptionExpiredNotification.php
│   ├── SendInvoiceEmail.php
│   ├── ProcessSubscriptionRenewals.php
│   ├── ProcessExpiredSubscriptions.php
│   └── SendTrialExpiringReminder.php
├── Middleware/                       # 4 middleware classes
│   ├── EnsureSubscriptionIsActive.php
│   ├── EnsureFeatureAccess.php
│   ├── EnsureHotelLimit.php
│   └── EnsureBookingLimit.php
├── Policies/                         # 2 policy classes
│   ├── SubscriptionPolicy.php
│   └── SubscriptionInvoicePolicy.php
├── Requests/                         # 3 form requests
│   ├── CreateSubscriptionRequest.php
│   ├── UpgradeSubscriptionRequest.php
│   └── CancelSubscriptionRequest.php
├── Resources/                        # 4 API resources
│   ├── SubscriptionPlanResource.php
│   ├── SubscriptionResource.php
│   ├── SubscriptionInvoiceResource.php
│   └── SubscriptionUsageResource.php
├── Controllers/                      # 3 API controllers
│   ├── SubscriptionPlanController.php
│   ├── SubscriptionController.php
│   └── SubscriptionInvoiceController.php
├── Actions/                          # 3 action classes
│   ├── CreateSubscriptionAction.php
│   ├── ExpireSubscriptionAction.php
│   └── CancelSubscriptionAction.php
├── Routes/
│   └── api.php                       # 11 REST endpoints
├── Providers/
│   └── SubscriptionServiceProvider.php
├── Tests/
│   ├── TestCase.php
│   ├── SubscriptionServiceTest.php
│   └── SubscriptionApiTest.php
├── README.md                         # Full documentation
├── INSTALLATION.md                   # Setup guide
├── EXAMPLES.md                       # Implementation examples
└── API.md                            # API reference (placeholder)
```

## Quick Commands

### Installation
```bash
# Register provider in config/app.php

# Publish config
php artisan vendor:publish --tag=subscription-config

# Run migrations
php artisan migrate

# Seed plans
php artisan db:seed --class="Modules\\Subscription\\Database\\Seeders\\SubscriptionPlanSeeder"

# Start queue worker
php artisan queue:work
```

### Development
```bash
# Run tests
php artisan test Modules/Subscription/Tests

# Clear caches
php artisan cache:clear

# Tinker
php artisan tinker
>>> $sub = Subscription::first();
>>> $sub->status; // Check status
```

## API Endpoints Summary

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/v1/subscription-plans` | List all plans |
| GET | `/api/v1/subscription-plans/{id}` | Get plan |
| GET | `/api/v1/subscription-plans/slug/{slug}` | Get by slug |
| POST | `/api/v1/subscription-plans/compare` | Compare plans |
| GET | `/api/v1/subscriptions` | Get org subscription |
| POST | `/api/v1/subscriptions` | Create subscription |
| POST | `/api/v1/subscriptions/upgrade` | Upgrade plan |
| POST | `/api/v1/subscriptions/downgrade` | Downgrade plan |
| POST | `/api/v1/subscriptions/cancel` | Cancel subscription |
| GET | `/api/v1/subscriptions/usage` | Get usage stats |
| GET\|POST | `/api/v1/subscription-invoices` | Invoices |

## Key Service Methods

### SubscriptionService
- `create(CreateSubscriptionDTO)` - Create new subscription
- `upgrade(UpgradeSubscriptionDTO, userId)` - Upgrade plan
- `downgrade(UpgradeSubscriptionDTO, userId)` - Downgrade plan
- `cancel(subscriptionId, reason)` - Cancel subscription
- `renew(subscriptionId)` - Renew subscription
- `expire(subscriptionId)` - Mark as expired
- `suspend(subscriptionId, reason)` - Suspend subscription
- `reactivate(subscriptionId)` - Reactivate cancelled
- `hasFeatureAccess(organizationId, featureKey)` - Check feature
- `getActiveSubscription(organizationId)` - Get active sub
- `canUpgrade(subscriptionId, newPlanId)` - Check upgrade
- `canDowngrade(subscriptionId, newPlanId)` - Check downgrade
- `validateTransition(subscription, targetStatus)` - Validate state

### InvoiceService
- `generateInvoice(subscription)` - Generate invoice
- `generateProratedInvoice(subscription, newPlan, fromDate, toDate)` - Prorate
- `calculateTax(amount)` - Calculate tax
- `calculateProration(amount, fromDate, toDate, cycleStart, cycleEnd)` - Prorating calc

### LimitValidator
- `validateAndTrackHotel(organizationId)` - Validate hotel limit
- `validateAndTrackStaff(organizationId)` - Validate staff limit
- `validateAndTrackRooms(organizationId)` - Validate room limit
- `validateAndTrackBooking(organizationId)` - Validate booking limit
- `getUsageStats(organizationId)` - Get usage stats
- `getRemainingQuota(organizationId)` - Get remaining quota

## Model Status Flows

### Subscription Statuses
```
TRIAL ---> ACTIVE ---> PAST_DUE ---> EXPIRED
              |         |
              |         v
              |      (grace period)
              |         |
              +---> CANCELLED
              |
              +---> SUSPENDED ---> ACTIVE (reactivate)
```

### Invoice Statuses
```
PENDING ---> PAID
   |
   v
FAILED --> RETRY ---> PAID
   |
   v
REFUNDED
```

## Configuration Keys

```php
'grace_period_days' => 7           // After due date
'trial_notification_days' => 3     // Before trial end
'invoice_due_days' => 14           // Invoice payment deadline
'tax_rate' => 0.10                 // 10% tax
'payment_gateways' => [            // Enable/disable
    'stripe' => true,
    'razorpay' => false,
]
'email_notifications' => [         // Notification toggles
    'send_welcome_email' => true,
    'send_expiration_reminder' => true,
    'send_trial_ending_reminder' => true,
    'send_invoice_email' => true,
]
'cache' => [
    'enabled' => true,             // Use caching
    'ttl_minutes' => 60,           // Cache duration
]
```

## Important Notes

### Multi-Tenant Safety
- All queries filtered by `organization_id`
- Policies check tenant isolation
- Subscriptions are 1:1 with Organization

### Database Constraints
- Index names limited to 64 characters (MySQL)
- Uses table prefixes: `org_`, `usr_`, `sub_`, `inv_`, etc.
- Soft deletes for historical data retention

### Performance Considerations
- Subscription data cached (1 hour)
- Eager load relationships
- Queue jobs for email/processing
- Database indexes on frequent queries

### Security Features
- Policy authorization on all operations
- Audit trail in subscription_histories
- Transaction wrapping for consistency
- Input validation via Form Requests

## Common Patterns

### Protecting Routes
```php
Route::middleware('subscription.active')->group(function () {
    Route::get('/dashboard', 'DashboardController');
});
```

### Checking Feature
```php
if ($subscription->hasFeature('advanced_reporting')) {
    // Show feature
}
```

### Validating Limits
```php
try {
    $limitValidator->validateAndTrackHotel($orgId);
} catch (SubscriptionException $e) {
    return response()->json(['error' => $e->getMessage()], 402);
}
```

### Creating Invoice
```php
$invoice = $invoiceService->generateInvoice($subscription);
SendInvoiceEmail::dispatch($invoice);
```

## Troubleshooting

### Subscription not found
- Check organization_id
- Verify subscription exists
- Check soft delete flag

### Limit validation failing
- Check current usage
- Verify plan limits
- Review subscription status

### Queue jobs not processing
- Start queue worker: `php artisan queue:work`
- Check failed jobs: `php artisan queue:failed`
- Verify Redis/database connection

### Cache issues
- Clear: `php artisan cache:clear`
- Verify Redis connection
- Check TTL settings

## Next Steps

1. Register service provider in `config/app.php`
2. Run migrations: `php artisan migrate`
3. Seed plans: `php artisan db:seed --class=SubscriptionPlanSeeder`
4. Configure payment gateway
5. Set up queue worker
6. Create scheduled tasks
7. Customize email templates
8. Deploy to production

For detailed information, see:
- [README.md](./README.md) - Full documentation
- [INSTALLATION.md](./INSTALLATION.md) - Setup guide
- [EXAMPLES.md](./EXAMPLES.md) - Code examples
- [API.md](./API.md) - API reference
