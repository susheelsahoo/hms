# Subscription Module - Implementation Examples

## Basic Usage Examples

### Example 1: Create Subscription on Organization Signup

```php
namespace App\Http\Controllers;

use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\DTOs\CreateSubscriptionDTO;

class OrganizationController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function store(StoreOrganizationRequest $request)
    {
        $organization = Organization::create($request->validated());

        // Automatically create trial subscription
        $trialPlan = SubscriptionPlan::where('slug', 'trial')->first();

        $this->subscriptionService->create(
            new CreateSubscriptionDTO(
                organizationId: $organization->id,
                subscriptionPlanId: $trialPlan->id,
                billingCycle: 'monthly',
                autoRenew: true,
            )
        );

        return response()->json([
            'message' => 'Organization created with trial subscription',
            'organization' => $organization,
        ]);
    }
}
```

### Example 2: Enforce Hotel Limit on Creation

```php
namespace App\Http\Controllers;

use Modules\Subscription\Services\LimitValidator;
use Modules\Subscription\Exceptions\SubscriptionException;

class HotelController extends Controller
{
    public function __construct(
        private LimitValidator $limitValidator
    ) {}

    public function store(StoreHotelRequest $request)
    {
        try {
            // Validate and track hotel
            $this->limitValidator->validateAndTrackHotel(
                auth()->user()->organization_id
            );

            $hotel = Hotel::create($request->validated() + [
                'organization_id' => auth()->user()->organization_id,
            ]);

            return response()->json($hotel, 201);
        } catch (SubscriptionException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                402 // Payment Required
            );
        }
    }
}
```

### Example 3: Check Feature Access

```php
namespace App\Http\Controllers;

use Modules\Subscription\Services\SubscriptionService;

class ReportController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function advanced(Request $request)
    {
        if (!$this->subscriptionService->hasFeatureAccess(
            $request->user()->organization_id,
            'advanced_reporting'
        )) {
            return response()->json(
                ['error' => 'Advanced reporting not available in your plan'],
                403
            );
        }

        // Generate advanced report
        return $this->generateAdvancedReport($request);
    }
}
```

### Example 4: Handle Subscription Upgrade

```php
namespace App\Http\Controllers;

use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\DTOs\UpgradeSubscriptionDTO;
use Modules\Subscription\Enums\BillingCycle;

class SubscriptionUpgradeController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function upgrade(UpgradeSubscriptionRequest $request)
    {
        try {
            $subscription = Subscription::where(
                'organization_id',
                $request->user()->organization_id
            )->firstOrFail();

            $dto = new UpgradeSubscriptionDTO(
                subscriptionId: $subscription->id,
                newPlanId: $request->input('plan_id'),
                billingCycle: BillingCycle::from(
                    $request->input('billing_cycle', 'monthly')
                ),
                prorate: true,
            );

            $upgraded = $this->subscriptionService->upgrade(
                $dto,
                $request->user()->id
            );

            return response()->json([
                'message' => 'Subscription upgraded successfully',
                'subscription' => $upgraded,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

## Advanced Usage Examples

### Example 5: Custom Plan with Feature Limits

```php
// Creating a custom plan
$customPlan = SubscriptionPlan::create([
    'name' => 'Custom Enterprise',
    'slug' => 'custom-enterprise',
    'description' => 'Custom plan for large enterprises',
    'price_monthly' => 5000,
    'price_yearly' => 50000,
    'hotel_limit' => 100,
    'staff_limit' => 500,
    'room_limit' => 50000,
    'booking_limit' => 500000,
    'storage_limit' => 5000,
    'is_active' => true,
    'metadata' => [
        'custom_sso' => true,
        'dedicated_support' => true,
        'custom_integration' => true,
    ],
]);

// Add features
$customPlan->features()->create([
    'feature_key' => 'advanced_analytics',
    'feature_name' => 'Advanced Analytics',
    'is_included' => true,
    'limits' => ['data_retention_days' => 730],
]);
```

### Example 6: Batch Operations on Trial Ending Subscriptions

```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Enums\SubscriptionStatus;

class ProcessTrialEndingSubscriptions extends Command
{
    public function handle()
    {
        $subscriptions = Subscription::query()
            ->where('status', SubscriptionStatus::TRIAL)
            ->where('trial_ends_at', '<=', now()->addDays(3))
            ->get();

        foreach ($subscriptions as $subscription) {
            // Send reminder email
            SendTrialEndingReminder::dispatch($subscription);

            // Log action
            \Log::info("Trial ending reminder queued for subscription {$subscription->id}");
        }

        $this->info("Processed {$subscriptions->count()} trial subscriptions");
    }
}
```

### Example 7: Usage Statistics Dashboard

```php
namespace App\Http\Controllers;

use Modules\Subscription\Services\LimitValidator;

class DashboardController extends Controller
{
    public function __construct(
        private LimitValidator $limitValidator
    ) {}

    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $stats = $this->limitValidator->getUsageStats($organizationId);

        return view('dashboard', [
            'subscription' => Subscription::where(
                'organization_id',
                $organizationId
            )->with('plan')->first(),
            'usage' => $stats,
            'usage_warning_threshold' => 80, // Show warning at 80%
        ]);
    }
}
```

### Example 8: Payment Webhook Handler

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Subscription\Models\SubscriptionInvoice;

class PaymentWebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->json()->all();

        if ($payload['type'] === 'invoice.payment_succeeded') {
            $invoice = SubscriptionInvoice::where(
                'transaction_id',
                $payload['data']['object']['id']
            )->first();

            if ($invoice) {
                $invoice->markAsPaid(
                    $payload['data']['object']['id'],
                    'stripe'
                );

                \Log::info("Invoice {$invoice->invoice_number} marked as paid");
            }
        }

        return response()->json(['success' => true]);
    }
}
```

### Example 9: Subscription History Report

```php
namespace App\Http\Controllers;

use Modules\Subscription\Models\SubscriptionHistory;

class SubscriptionReportController extends Controller
{
    public function history(Request $request)
    {
        $organizationId = $request->user()->organization_id;

        $history = SubscriptionHistory::where(
            'organization_id',
            $organizationId
        )
            ->with('subscription', 'oldPlan', 'newPlan', 'changedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('subscriptions.history', ['history' => $history]);
    }
}
```

### Example 10: Downgrade with Warning

```php
namespace App\Http\Controllers;

use Modules\Subscription\Services\SubscriptionService;

class SubscriptionDowngradeController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function downgrade(Request $request)
    {
        $subscription = Subscription::where(
            'organization_id',
            $request->user()->organization_id
        )->firstOrFail();

        // Check if downgrade would violate limits
        $newPlan = SubscriptionPlan::find($request->input('plan_id'));
        $usage = $subscription->usage;

        $warnings = [];

        if ($usage->hotels_used > $newPlan->hotel_limit) {
            $warnings[] = "Your organization has more hotels than this plan allows. "
                . "Please delete " . ($usage->hotels_used - $newPlan->hotel_limit) . " hotels first.";
        }

        if ($warnings) {
            return response()->json([
                'warnings' => $warnings,
                'action_required' => true,
            ], 409); // Conflict
        }

        // Proceed with downgrade
        try {
            $downgraded = $this->subscriptionService->downgrade(
                new UpgradeSubscriptionDTO(...),
                $request->user()->id
            );

            return response()->json([
                'message' => 'Subscription downgraded successfully',
                'subscription' => $downgraded,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

## Testing Examples

### Example 11: Test Subscription Creation

```php
namespace Modules\Subscription\Tests\Feature;

use Modules\Subscription\Tests\TestCase;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\DTOs\CreateSubscriptionDTO;

class SubscriptionCreationTest extends TestCase
{
    public function test_can_create_trial_subscription()
    {
        $service = app(SubscriptionService::class);
        $organization = Organization::factory()->create();
        $trialPlan = SubscriptionPlan::where('slug', 'trial')->first();

        $subscription = $service->create(
            new CreateSubscriptionDTO(
                organizationId: $organization->id,
                subscriptionPlanId: $trialPlan->id,
            )
        );

        $this->assertTrue($subscription->isTrial());
        $this->assertNotNull($subscription->trial_ends_at);
    }
}
```

### Example 12: Test Limit Enforcement

```php
namespace Modules\Subscription\Tests\Feature;

use Modules\Subscription\Tests\TestCase;
use Modules\Subscription\Services\LimitValidator;
use Modules\Subscription\Exceptions\SubscriptionException;

class LimitEnforcementTest extends TestCase
{
    public function test_hotel_limit_enforced()
    {
        $validator = app(LimitValidator::class);
        $organization = Organization::factory()->create();
        $plan = SubscriptionPlan::factory()->create(['hotel_limit' => 1]);

        $subscription = Subscription::factory()->create([
            'organization_id' => $organization->id,
            'subscription_plan_id' => $plan->id,
        ]);

        $usage = SubscriptionUsage::factory()->create([
            'subscription_id' => $subscription->id,
            'hotels_used' => 1, // Already at limit
        ]);

        $this->expectException(SubscriptionException::class);
        $validator->validateAndTrackHotel($organization->id);
    }
}
```

## Integration Patterns

### Pattern 1: Subscription Middleware Protection

```php
Route::middleware([
    'auth:sanctum',
    'subscription.active',
    'subscription.feature:advanced_reporting',
])->group(function () {
    Route::get('/reports/advanced', 'ReportController@advanced');
});
```

### Pattern 2: Conditional Feature Display

```blade
@if(auth()->user()->subscription->hasFeature('custom_branding'))
    <button>Customize Branding</button>
@else
    <button disabled title="Available in Premium plan">Customize Branding</button>
@endif
```

### Pattern 3: Graceful Limit Handling

```php
try {
    $limitValidator->validateAndTrackBooking($organizationId);
    // Create booking
} catch (SubscriptionException $e) {
    // Show upgrade offer
    return redirect()->route('subscriptions.upgrade')
        ->with('upgrade_reason', $e->getMessage());
}
```

## Best Practices

1. **Always use DTOs** for data transfer between layers
2. **Use transactions** for state-changing operations
3. **Leverage events** for side effects and notifications
4. **Cache aggressively** but invalidate properly
5. **Log important events** for audit trails
6. **Test edge cases** thoroughly
7. **Handle exceptions gracefully** in controllers
8. **Use queue jobs** for long-running operations
9. **Validate thoroughly** before state changes
10. **Monitor subscription health** regularly
