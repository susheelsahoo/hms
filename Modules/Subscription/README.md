# Subscription Module - Enterprise SaaS Architecture

## Overview

The Subscription Module is an enterprise-grade subscription management system for a Hotel Management System (HMS) SaaS application. It handles subscription lifecycle, billing, feature access control, and resource limits management with event-driven architecture.

## Key Features

### 1. **Subscription Management**

- Multiple subscription plans (Basic, Professional, Enterprise, Trial)
- Plan upgrade/downgrade functionality
- Automatic renewal with grace period support
- Subscription cancellation and reactivation

### 2. **Billing & Invoicing**

- Monthly and yearly billing cycles
- Automatic invoice generation
- Prorated billing for plan changes
- Invoice tracking and payment management
- Support for multiple payment gateways (Stripe, Razorpay ready)

### 3. **Feature Access Control**

- Feature-based access management
- Per-plan feature configuration
- Feature limit management
- Dynamic feature availability

### 4. **Resource Limits**

- Hotel limit enforcement
- Staff/user limit tracking
- Room limit management
- Booking limit enforcement (monthly)
- Storage limit tracking

### 5. **Usage Tracking**

- Real-time resource usage monitoring
- Automatic usage reset on billing period
- Usage percentage calculations
- Quota remaining calculations

### 6. **Event-Driven Architecture**

- Events: SubscriptionCreated, SubscriptionExpired, SubscriptionCancelled, PlanUpgraded, PlanDowngraded, InvoiceGenerated
- Async event listeners
- Queue-based email notifications
- Audit logging

## Database Schema

### subscription_plans

- Stores plan definitions with pricing and limits
- Supports trial plans
- Metadata for future extensions
- Soft deletes for archival

### subscriptions

- One subscription per organization (tenant)
- Status tracking: trial, active, past_due, expired, cancelled, suspended
- Trial period and grace period support
- Renewal scheduling
- Auto-renewal toggle

### subscription_usages

- Tracks resource consumption per subscription
- Monthly reset for booking limits
- Usage percentage calculations
- Real-time quota checking

### subscription_invoices

- Invoice generation and tracking
- Payment status management
- Payment gateway metadata storage
- Tax calculation support
- Overdue invoice tracking

### subscription_histories

- Audit trail of all subscription changes
- Plan change tracking
- User who made the change
- Action timestamps
- Metadata for analysis

### subscription_features

- Maps features to plans
- Feature inclusion/exclusion per plan
- Flexible limit configuration
- Feature metadata

## Architecture Patterns

### 1. **Repository Pattern**

- `SubscriptionRepository` - Data access for subscriptions
- `SubscriptionPlanRepository` - Plan management
- `SubscriptionUsageRepository` - Usage tracking
- Interface-based design for testability

### 2. **Service Layer**

- `SubscriptionService` - Core business logic
- `InvoiceService` - Invoice management
- `LimitValidator` - Resource limit enforcement
- Centralized business logic

### 3. **DTO Pattern**

- `CreateSubscriptionDTO` - Type-safe subscription creation
- `UpgradeSubscriptionDTO` - Plan change data
- `SubscriptionUsageDTO` - Usage tracking
- `SubscriptionInvoiceDTO` - Invoice generation

### 4. **Event-Driven Architecture**

- Event classes with dispatchable trait
- Event listeners for side effects
- Async job processing
- Audit logging through events

### 5. **Policy-Based Authorization**

- `SubscriptionPolicy` - Subscription access control
- `SubscriptionInvoicePolicy` - Invoice access control
- Tenant isolation verification
- Admin-only operations

## Usage Examples

### Create Subscription

```php
$subscriptionService->create(
    new CreateSubscriptionDTO(
        organizationId: 1,
        subscriptionPlanId: 2,
        billingCycle: 'monthly',
        autoRenew: true
    )
);
```

### Upgrade Plan

```php
$subscriptionService->upgrade(
    new UpgradeSubscriptionDTO(
        subscriptionId: 1,
        newPlanId: 3,
        billingCycle: BillingCycle::YEARLY,
        prorate: true
    ),
    userId: auth()->id()
);
```

### Validate Resource Limits

```php
// Check hotel limit
$limitValidator->validateAndTrackHotel($organizationId);

// Get usage statistics
$stats = $limitValidator->getUsageStats($organizationId);
```

### Check Feature Access

```php
if ($subscriptionService->hasFeatureAccess($organizationId, 'advanced_reporting')) {
    // Grant access to advanced reporting
}
```

## Middleware

### EnsureSubscriptionIsActive

Ensures organization has active subscription

```php
Route::get('/dashboard', 'DashboardController@index')
    ->middleware('subscription.active');
```

### EnsureFeatureAccess

Ensures organization has access to specific feature

```php
Route::post('/reports/advanced', 'ReportsController@advanced')
    ->middleware('subscription.feature:advanced_reporting');
```

### EnsureHotelLimit

Enforces hotel creation limits

```php
Route::post('/hotels', 'HotelController@store')
    ->middleware('subscription.hotel-limit');
```

### EnsureBookingLimit

Enforces booking creation limits

```php
Route::post('/bookings', 'BookingController@store')
    ->middleware('subscription.booking-limit');
```

## API Endpoints

### Plans

- `GET /api/v1/subscription-plans` - List active plans
- `GET /api/v1/subscription-plans/{id}` - Get plan details
- `GET /api/v1/subscription-plans/slug/{slug}` - Get plan by slug
- `POST /api/v1/subscription-plans/compare` - Compare multiple plans

### Subscriptions

- `GET /api/v1/subscriptions` - Get organization subscription
- `POST /api/v1/subscriptions` - Create new subscription
- `POST /api/v1/subscriptions/upgrade` - Upgrade plan
- `POST /api/v1/subscriptions/downgrade` - Downgrade plan
- `POST /api/v1/subscriptions/cancel` - Cancel subscription
- `GET /api/v1/subscriptions/usage` - Get usage statistics

### Invoices

- `GET /api/v1/subscription-invoices` - List invoices
- `GET /api/v1/subscription-invoices/{invoiceNumber}` - Get invoice
- `GET /api/v1/subscription-invoices/{invoiceNumber}/download` - Download invoice
- `GET /api/v1/subscription-invoices/overdue` - Get overdue invoices

## Enums

### SubscriptionStatus

- `TRIAL` - Trial period
- `ACTIVE` - Active subscription
- `PAST_DUE` - Payment overdue
- `EXPIRED` - Subscription expired
- `CANCELLED` - Subscription cancelled
- `SUSPENDED` - Temporarily suspended

### BillingCycle

- `MONTHLY` - Monthly billing
- `YEARLY` - Annual billing

### SubscriptionAction

- `CREATED` - New subscription created
- `UPGRADE` - Plan upgraded
- `DOWNGRADE` - Plan downgraded
- `RENEWAL` - Subscription renewed
- `CANCELLATION` - Subscription cancelled
- `REACTIVATION` - Cancelled subscription reactivated
- `SUSPENSION` - Subscription suspended
- `EXPIRATION` - Subscription expired
- `TRIAL_STARTED` - Trial started
- `TRIAL_ENDED` - Trial ended

### InvoiceStatus

- `PENDING` - Invoice pending
- `PAID` - Invoice paid
- `FAILED` - Payment failed
- `REFUNDED` - Invoice refunded

## Caching Strategy

### Cache Keys

- `subscription.org.{organizationId}` - Organization's active subscription (1 hour TTL)
- Plan information cached in models
- Feature availability cached with subscription

### Cache Invalidation

- Automatic on subscription changes
- Manual clearing on plan updates
- Event-driven invalidation

## Queue Jobs

### SendSubscriptionWelcomeEmail

Sends welcome email after subscription creation

### SendSubscriptionExpiredNotification

Sends notification when subscription expires

### SendInvoiceEmail

Sends invoice to organization

### ProcessSubscriptionRenewals

Processes auto-renewals (scheduled job)

### ProcessExpiredSubscriptions

Handles expired subscriptions (scheduled job)

### SendTrialExpiringReminder

Sends reminder 3 days before trial expiration

## Future Enhancements

### 1. **Payment Gateway Integration**

- Stripe integration
- Razorpay integration
- Payment retry logic
- Webhook handling

### 2. **Advanced Features**

- Coupon system
- Addon management
- Metered billing
- Custom pricing

### 3. **Reporting**

- Subscription analytics
- Revenue reports
- Churn analysis
- Usage trends

### 4. **Automation**

- Smart renewal management
- Automatic downgrade on non-payment
- Grace period management
- Dunning management

## Testing

### Unit Tests

```php
class SubscriptionServiceTest extends TestCase
{
    public function test_can_create_subscription()
    {
        $dto = new CreateSubscriptionDTO(...);
        $subscription = $this->service->create($dto);
        $this->assertNotNull($subscription->id);
    }
}
```

### Feature Tests

- Integration tests for workflows
- API endpoint tests
- Middleware tests
- Policy tests

## Configuration

Configuration file: `config/subscription.php`

```php
return [
    'grace_period_days' => 7,
    'trial_notification_days' => 3,
    'invoice_due_days' => 14,
    'tax_rate' => 0.10,
    'payment_gateways' => [
        'stripe' => true,
        'razorpay' => true,
    ],
];
```

## Database Indexes

All tables include optimized indexes for:

- Organization queries
- Status filtering
- Date range queries
- Payment method lookups

### Index Naming Convention

- `{table_prefix}_{columns}_{purpose}_idx`
- Examples: `sub_status_idx`, `bk_payment_idx`, `pay_gateway_txn_idx`

## Security Considerations

1. **Multi-Tenant Safety**
   - All queries filtered by organization_id
   - Policy authorization checks
   - Audit logging of changes

2. **Data Integrity**
   - Transactions for state changes
   - Soft deletes for historical data
   - Event logging for audit trails

3. **Rate Limiting**
   - API endpoints throttled (60 req/min)
   - Queue job rate limiting
   - Email rate limiting

## Performance Optimization

1. **Database**
   - Indexed columns for fast lookups
   - Efficient pagination
   - Eager loading of relationships

2. **Caching**
   - Subscription cache (1 hour)
   - Plan information cached
   - Query result caching

3. **Queues**
   - Email sending async
   - Invoice generation background
   - Renewal processing scheduled

## Monitoring & Debugging

### Logging

- Event logging for all state changes
- Error logging for exceptions
- Audit trail in subscription_histories

### Debugging

- Event debugging with listeners
- Database query logging
- Cache debugging with artisan commands

## Support & Troubleshooting

### Common Issues

1. **Subscription not found**
   - Check organization_id
   - Verify subscription exists
   - Check soft delete status

2. **Limit exceeded errors**
   - Check current usage
   - Verify plan limits
   - Review usage tracking

3. **Invoice generation failed**
   - Check invoice prerequisites
   - Verify tax calculation
   - Review payment method

## Contributing

Guidelines for extending the module:

1. Follow DDD principles
2. Use service layer for business logic
3. Add events for side effects
4. Write comprehensive tests
5. Update documentation

## License

This module is part of the HMS application and follows the same license.
