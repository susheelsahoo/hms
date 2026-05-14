# Subscription Module - Installation & Setup Guide

## Prerequisites

- Laravel 13+
- PHP 8.3+
- MySQL 5.7+ or PostgreSQL 10+
- Redis (optional, for queue processing)

## Installation Steps

### 1. Register Service Provider

Add the Subscription Service Provider to your `config/app.php`:

```php
'providers' => [
    // ... other providers
    Modules\Subscription\Providers\SubscriptionServiceProvider::class,
],
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=subscription-config
```

This creates `config/subscription.php` with default settings.

### 3. Run Migrations

```bash
php artisan migrate
```

This will run all subscription module migrations in order:

- `2026_05_14_000000_create_subscription_plans_table`
- `2026_05_14_000010_create_subscriptions_table`
- `2026_05_14_000020_create_subscription_usages_table`
- `2026_05_14_000030_create_subscription_invoices_table`
- `2026_05_14_000040_create_subscription_histories_table`
- `2026_05_14_000050_create_subscription_features_table`

### 4. Seed Subscription Plans

```bash
php artisan db:seed --class="Modules\\Subscription\\Database\\Seeders\\SubscriptionPlanSeeder"
```

This seeds four default plans:

- **Trial**: 14-day free trial (1 hotel, 5 staff, 50 rooms, 500 bookings)
- **Basic**: $29.99/month (1 hotel, 10 staff, 100 rooms, 2000 bookings)
- **Professional**: $79.99/month (5 hotels, 50 staff, 1000 rooms, 10000 bookings)
- **Enterprise**: $199.99/month (unlimited hotels, staff, rooms, bookings)

### 5. Register Event Listeners

Events are automatically registered in the Service Provider. Ensure your event bus is configured properly in `config/event.php`.

### 6. Configure Queue Jobs

Add queue configuration to `.env`:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Or use database queue:

```env
QUEUE_CONNECTION=database
php artisan queue:table
php artisan migrate
```

### 7. Update Organization Model

Add relationship to the Organization model in `app/Models/Organization.php`:

```php
use Modules\Subscription\Models\Subscription;

class Organization extends Model
{
    // ... existing code

    public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class);
    }
}
```

### 8. Register Policies

Policies are auto-registered in the Service Provider. To use them in controllers:

```php
public function update(User $user, Subscription $subscription)
{
    $this->authorize('update', $subscription);
}
```

### 9. Set Up Scheduled Tasks

Add to `app/Console/Kernel.php`:

```php
use Modules\Subscription\Jobs\ProcessSubscriptionRenewals;
use Modules\Subscription\Jobs\ProcessExpiredSubscriptions;
use Modules\Subscription\Jobs\SendTrialExpiringReminder;

protected function schedule(Schedule $schedule)
{
    // Process renewals every day at midnight
    $schedule->job(new ProcessSubscriptionRenewals())
        ->daily()
        ->at('00:00');

    // Check for expired subscriptions every hour
    $schedule->job(new ProcessExpiredSubscriptions())
        ->hourly();

    // Send trial reminders daily
    $schedule->job(new SendTrialExpiringReminder())
        ->daily()
        ->at('09:00');
}
```

### 10. Start Queue Worker

For processing async jobs:

```bash
php artisan queue:work
```

Or for production:

```bash
php artisan queue:work --daemon --tries=3 --timeout=60
```

## Configuration

### Key Configuration Options

```php
// config/subscription.php

return [
    // Grace period after payment is due (days)
    'grace_period_days' => 7,

    // Days before trial end to send reminder
    'trial_notification_days' => 3,

    // Days until invoice is due
    'invoice_due_days' => 14,

    // Tax rate for invoice calculation
    'tax_rate' => 0.10, // 10%

    // Payment gateway enablement
    'payment_gateways' => [
        'stripe' => true,
        'razorpay' => false,
    ],

    // Email notification settings
    'email_notifications' => [
        'send_welcome_email' => true,
        'send_expiration_reminder' => true,
        'send_trial_ending_reminder' => true,
        'send_invoice_email' => true,
    ],

    // Caching
    'cache' => [
        'enabled' => true,
        'ttl_minutes' => 60,
    ],
];
```

## Environment Variables

Add to your `.env` file:

```env
# Subscription settings
SUBSCRIPTION_GRACE_PERIOD_DAYS=7
SUBSCRIPTION_TRIAL_NOTIFICATION_DAYS=3
SUBSCRIPTION_INVOICE_DUE_DAYS=14
SUBSCRIPTION_TAX_RATE=0.10
SUBSCRIPTION_CACHE_ENABLED=true
SUBSCRIPTION_CACHE_TTL=60

# Payment gateways
PAYMENT_STRIPE_ENABLED=true
PAYMENT_RAZORPAY_ENABLED=false

# Email notifications
SUBSCRIPTION_SEND_WELCOME_EMAIL=true
SUBSCRIPTION_SEND_EXPIRATION_REMINDER=true
SUBSCRIPTION_SEND_TRIAL_ENDING_REMINDER=true
SUBSCRIPTION_SEND_INVOICE_EMAIL=true
```

## Middleware Registration

Register middleware in `app/Http/Kernel.php`:

```php
use Modules\Subscription\Middleware\EnsureSubscriptionIsActive;
use Modules\Subscription\Middleware\EnsureFeatureAccess;
use Modules\Subscription\Middleware\EnsureHotelLimit;
use Modules\Subscription\Middleware\EnsureBookingLimit;

protected $routeMiddleware = [
    // ... existing middleware
    'subscription.active' => EnsureSubscriptionIsActive::class,
    'subscription.feature' => EnsureFeatureAccess::class,
    'subscription.hotel-limit' => EnsureHotelLimit::class,
    'subscription.booking-limit' => EnsureBookingLimit::class,
];
```

## Testing

### Run Tests

```bash
php artisan test Modules/Subscription/Tests
```

### Run Specific Test

```bash
php artisan test Modules/Subscription/Tests/SubscriptionServiceTest
```

## Database Backup

Before production deployment:

```bash
php artisan backup:run

# Or use mysqldump for MySQL
mysqldump -u user -p database > backup.sql

# Or pg_dump for PostgreSQL
pg_dump database > backup.sql
```

## Performance Tuning

### Database Indexes

All necessary indexes are created by migrations. Verify:

```sql
-- MySQL
SHOW INDEX FROM subscription_plans;
SHOW INDEX FROM subscriptions;
SHOW INDEX FROM subscription_usages;
SHOW INDEX FROM subscription_invoices;
SHOW INDEX FROM subscription_histories;
SHOW INDEX FROM subscription_features;

-- PostgreSQL
\d subscription_plans
\d subscriptions
\d subscription_usages
\d subscription_invoices
\d subscription_histories
\d subscription_features
```

### Redis Cache

For production, configure Redis:

```env
CACHE_DRIVER=redis
CACHE_REDIS_CONNECTION=default

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

### Query Optimization

Monitor slow queries:

```bash
php artisan tinker
>>> DB::enableQueryLog()
>>> // run your queries
>>> collect(DB::getQueryLog())->pluck('time', 'query')->all()
```

## Troubleshooting

### Issue: Migrations not found

**Solution**: Ensure service provider is registered before running migrations:

```bash
php artisan migrate --path="Modules/Subscription/Database/Migrations"
```

### Issue: Service not available

**Solution**: Clear service provider cache:

```bash
php artisan config:cache
php artisan cache:clear
php artisan route:cache
```

### Issue: Queue jobs not processing

**Solution**: Verify queue worker is running:

```bash
# Check if worker is running
ps aux | grep "queue:work"

# Restart worker
php artisan queue:restart

# Check failed jobs
php artisan queue:failed
```

### Issue: Cache not invalidating

**Solution**: Manually clear cache:

```bash
php artisan cache:clear
php artisan cache:forget subscription.org.*
```

## Next Steps

1. **Implement Payment Gateway**: Integrate Stripe or Razorpay
2. **Customize Plans**: Create domain-specific subscription plans
3. **Add Features**: Configure plan features based on business needs
4. **Email Templates**: Customize notification emails
5. **Billing Reports**: Create revenue and subscription reports
6. **Analytics**: Monitor subscription metrics and churn

## Support Resources

- **Documentation**: See [README.md](./README.md)
- **API Documentation**: See [API.md](./API.md)
- **Examples**: See [examples/](./examples/)
- **Tests**: See [Tests/](./Tests/)

## Security Checklist

- [ ] Service provider registered
- [ ] Migrations executed
- [ ] Environment variables configured
- [ ] Queue worker running
- [ ] Policies registered
- [ ] Middleware registered
- [ ] CORS configured for API
- [ ] Rate limiting enabled
- [ ] Database backups scheduled
- [ ] Error logging configured
