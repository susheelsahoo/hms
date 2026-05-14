<?php

namespace Modules\Subscription\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\Subscription\Repositories\SubscriptionRepository;
use Modules\Subscription\Repositories\SubscriptionPlanRepository;
use Modules\Subscription\Repositories\SubscriptionUsageRepository;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionUsage;
use Modules\Subscription\Models\SubscriptionHistory;
use Modules\Subscription\Models\SubscriptionInvoice;
use Modules\Subscription\DTOs\CreateSubscriptionDTO;
use Modules\Subscription\DTOs\UpgradeSubscriptionDTO;
use Modules\Subscription\Enums\SubscriptionStatus;
use Modules\Subscription\Enums\SubscriptionAction;
use Modules\Subscription\Enums\BillingCycle;
use Modules\Subscription\Exceptions\SubscriptionException;
use App\Models\Organization;

class SubscriptionService
{
    private SubscriptionRepository $subscriptionRepo;
    private SubscriptionPlanRepository $planRepo;
    private SubscriptionUsageRepository $usageRepo;
    private InvoiceService $invoiceService;

    public function __construct(
        SubscriptionRepository $subscriptionRepo,
        SubscriptionPlanRepository $planRepo,
        SubscriptionUsageRepository $usageRepo,
        InvoiceService $invoiceService,
    ) {
        $this->subscriptionRepo = $subscriptionRepo;
        $this->planRepo = $planRepo;
        $this->usageRepo = $usageRepo;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Create a new subscription for organization
     */
    public function create(CreateSubscriptionDTO $dto): Subscription
    {
        return DB::transaction(function () use ($dto) {
            // Check if organization already has subscription
            if ($this->subscriptionRepo->findByOrganizationId($dto->organizationId)) {
                throw SubscriptionException::organizationAlreadySubscribed($dto->organizationId);
            }

            $plan = $this->planRepo->findById($dto->subscriptionPlanId);
            if (!$plan) {
                throw SubscriptionException::planNotFound($dto->subscriptionPlanId);
            }

            $billingCycle = BillingCycle::from($dto->billingCycle);
            $amount = $billingCycle === BillingCycle::MONTHLY
                ? $plan->getMonthlyPrice()
                : $plan->getYearlyPrice();

            $subscription = $this->subscriptionRepo->create([
                'organization_id' => $dto->organizationId,
                'subscription_plan_id' => $dto->subscriptionPlanId,
                'status' => $plan->is_trial ? SubscriptionStatus::TRIAL : SubscriptionStatus::ACTIVE,
                'billing_cycle' => $billingCycle->value,
                'starts_at' => now(),
                'ends_at' => now()->addDays($billingCycle->daysInCycle()),
                'trial_ends_at' => $plan->is_trial ? now()->addDays($plan->trial_days) : null,
                'renewal_at' => now()->addDays($billingCycle->daysInCycle()),
                'amount' => $amount,
                'currency' => 'USD',
                'auto_renew' => $dto->autoRenew,
                'metadata' => $dto->metadata,
            ]);

            // Create usage record
            $this->createUsageRecord($subscription);

            // Record history
            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::CREATED,
                description: "Subscription created for plan: {$plan->name}"
            );

            return $subscription;
        });
    }

    /**
     * Upgrade subscription to a higher plan
     */
    public function upgrade(UpgradeSubscriptionDTO $dto, ?int $userId = null): Subscription
    {
        return DB::transaction(function () use ($dto, $userId) {
            $subscription = $this->subscriptionRepo->findById($dto->subscriptionId);
            if (!$subscription) {
                throw SubscriptionException::subscriptionNotFound($dto->subscriptionId);
            }

            if (!$subscription->canUpgrade()) {
                throw SubscriptionException::invalidStatusTransition(
                    $subscription->status->value,
                    'upgrade'
                );
            }

            $newPlan = $this->planRepo->findById($dto->newPlanId);
            if (!$newPlan) {
                throw SubscriptionException::planNotFound($dto->newPlanId);
            }

            if ($subscription->subscription_plan_id === $dto->newPlanId) {
                throw SubscriptionException::cannotUpgradeSamePlan();
            }

            $oldPlan = $subscription->plan;
            $oldAmount = $subscription->getAmount();
            $newAmount = $dto->billingCycle === BillingCycle::MONTHLY
                ? $newPlan->getMonthlyPrice()
                : $newPlan->getYearlyPrice();

            // Update subscription
            $subscription->update([
                'subscription_plan_id' => $dto->newPlanId,
                'amount' => $newAmount,
                'billing_cycle' => $dto->billingCycle->value,
                'renewal_at' => now()->addDays($dto->billingCycle->daysInCycle()),
            ]);

            // Generate prorated invoice if applicable
            if ($dto->prorate && $oldAmount < $newAmount) {
                $this->invoiceService->generateProratedInvoice(
                    $subscription,
                    $oldAmount,
                    $newAmount
                );
            }

            // Record history
            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::UPGRADE,
                oldPlanId: $oldPlan->id,
                newPlanId: $newPlan->id,
                description: "Upgraded from {$oldPlan->name} to {$newPlan->name}",
                changedBy: $userId
            );

            // Clear cache
            $this->clearSubscriptionCache($subscription->organization_id);

            return $subscription->refresh();
        });
    }

    /**
     * Downgrade subscription to a lower plan
     */
    public function downgrade(UpgradeSubscriptionDTO $dto, ?int $userId = null): Subscription
    {
        return DB::transaction(function () use ($dto, $userId) {
            $subscription = $this->subscriptionRepo->findById($dto->subscriptionId);
            if (!$subscription) {
                throw SubscriptionException::subscriptionNotFound($dto->subscriptionId);
            }

            if (!$subscription->canDowngrade()) {
                throw SubscriptionException::invalidStatusTransition(
                    $subscription->status->value,
                    'downgrade'
                );
            }

            $newPlan = $this->planRepo->findById($dto->newPlanId);
            if (!$newPlan) {
                throw SubscriptionException::planNotFound($dto->newPlanId);
            }

            if ($subscription->subscription_plan_id === $dto->newPlanId) {
                throw SubscriptionException::cannotUpgradeSamePlan();
            }

            $oldPlan = $subscription->plan;

            // Update subscription
            $subscription->update([
                'subscription_plan_id' => $dto->newPlanId,
                'billing_cycle' => $dto->billingCycle->value,
                'renewal_at' => now()->addDays($dto->billingCycle->daysInCycle()),
            ]);

            // Record history
            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::DOWNGRADE,
                oldPlanId: $oldPlan->id,
                newPlanId: $newPlan->id,
                description: "Downgraded from {$oldPlan->name} to {$newPlan->name}",
                changedBy: $userId
            );

            $this->clearSubscriptionCache($subscription->organization_id);

            return $subscription->refresh();
        });
    }

    /**
     * Cancel subscription
     */
    public function cancel(int $subscriptionId, ?string $reason = null, ?int $userId = null): Subscription
    {
        return DB::transaction(function () use ($subscriptionId, $reason, $userId) {
            $subscription = $this->subscriptionRepo->findById($subscriptionId);
            if (!$subscription) {
                throw SubscriptionException::subscriptionNotFound($subscriptionId);
            }

            if (!$subscription->canCancel()) {
                throw SubscriptionException::invalidStatusTransition(
                    $subscription->status->value,
                    'cancel'
                );
            }

            $subscription->update([
                'status' => SubscriptionStatus::CANCELLED,
                'cancelled_at' => now(),
                'auto_renew' => false,
                'metadata' => array_merge($subscription->metadata ?? [], ['cancellation_reason' => $reason]),
            ]);

            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::CANCELLATION,
                description: $reason ?? 'Subscription cancelled',
                changedBy: $userId
            );

            $this->clearSubscriptionCache($subscription->organization_id);

            return $subscription->refresh();
        });
    }

    /**
     * Renew subscription
     */
    public function renew(int $subscriptionId): Subscription
    {
        return DB::transaction(function () use ($subscriptionId) {
            $subscription = $this->subscriptionRepo->findById($subscriptionId);
            if (!$subscription) {
                throw SubscriptionException::subscriptionNotFound($subscriptionId);
            }

            if (!$subscription->isRenewable()) {
                throw new SubscriptionException('Subscription is not eligible for renewal.');
            }

            $billingCycle = BillingCycle::from($subscription->billing_cycle);
            $plan = $subscription->plan;
            $amount = $billingCycle === BillingCycle::MONTHLY
                ? $plan->getMonthlyPrice()
                : $plan->getYearlyPrice();

            $subscription->update([
                'status' => SubscriptionStatus::ACTIVE,
                'starts_at' => now(),
                'ends_at' => now()->addDays($billingCycle->daysInCycle()),
                'renewal_at' => now()->addDays($billingCycle->daysInCycle()),
                'amount' => $amount,
            ]);

            // Generate invoice
            $this->invoiceService->generateInvoice($subscription);

            // Reset usage
            $subscription->usage?->resetMonthlyUsage();

            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::RENEWAL,
                description: 'Subscription renewed for ' . $billingCycle->label()
            );

            $this->clearSubscriptionCache($subscription->organization_id);

            return $subscription->refresh();
        });
    }

    /**
     * Expire subscription
     */
    public function expire(int $subscriptionId): Subscription
    {
        return DB::transaction(function () use ($subscriptionId) {
            $subscription = $this->subscriptionRepo->findById($subscriptionId);
            if (!$subscription) {
                throw SubscriptionException::subscriptionNotFound($subscriptionId);
            }

            $subscription->update([
                'status' => SubscriptionStatus::EXPIRED,
                'ends_at' => now(),
            ]);

            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::EXPIRATION,
                description: 'Subscription expired'
            );

            $this->clearSubscriptionCache($subscription->organization_id);

            return $subscription->refresh();
        });
    }

    /**
     * Suspend subscription (usually due to non-payment)
     */
    public function suspend(int $subscriptionId, ?string $reason = null): Subscription
    {
        return DB::transaction(function () use ($subscriptionId, $reason) {
            $subscription = $this->subscriptionRepo->findById($subscriptionId);
            if (!$subscription) {
                throw SubscriptionException::subscriptionNotFound($subscriptionId);
            }

            $subscription->update([
                'status' => SubscriptionStatus::SUSPENDED,
                'metadata' => array_merge($subscription->metadata ?? [], ['suspension_reason' => $reason]),
            ]);

            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::SUSPENSION,
                description: $reason ?? 'Subscription suspended'
            );

            $this->clearSubscriptionCache($subscription->organization_id);

            return $subscription->refresh();
        });
    }

    /**
     * Reactivate a cancelled subscription
     */
    public function reactivate(int $subscriptionId): Subscription
    {
        return DB::transaction(function () use ($subscriptionId) {
            $subscription = $this->subscriptionRepo->findById($subscriptionId);
            if (!$subscription) {
                throw SubscriptionException::subscriptionNotFound($subscriptionId);
            }

            if (!$subscription->isCancelled()) {
                throw new SubscriptionException('Only cancelled subscriptions can be reactivated.');
            }

            $billingCycle = BillingCycle::from($subscription->billing_cycle);

            $subscription->update([
                'status' => SubscriptionStatus::ACTIVE,
                'cancelled_at' => null,
                'starts_at' => now(),
                'ends_at' => now()->addDays($billingCycle->daysInCycle()),
                'renewal_at' => now()->addDays($billingCycle->daysInCycle()),
                'auto_renew' => true,
            ]);

            $this->recordHistory(
                subscription: $subscription,
                action: SubscriptionAction::REACTIVATION,
                description: 'Subscription reactivated'
            );

            $this->clearSubscriptionCache($subscription->organization_id);

            return $subscription->refresh();
        });
    }

    /**
     * Get organization's active subscription
     */
    public function getActiveSubscription(int $organizationId): ?Subscription
    {
        return Cache::remember(
            "subscription.org.{$organizationId}",
            now()->addHours(1),
            fn () => $this->subscriptionRepo->findByOrganizationId($organizationId)
        );
    }

    /**
     * Check if organization has feature access
     */
    public function hasFeatureAccess(int $organizationId, string $feature): bool
    {
        $subscription = $this->getActiveSubscription($organizationId);
        
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        return $subscription->hasFeature($feature);
    }

    /**
     * Validate hotel limit
     */
    public function validateHotelLimit(int $organizationId, int $count = 1): bool
    {
        $subscription = $this->getActiveSubscription($organizationId);
        
        if (!$subscription || !$subscription->isActive()) {
            throw SubscriptionException::noActiveSubscription($organizationId);
        }

        $usage = $subscription->usage;
        $limit = $subscription->plan->hotel_limit;

        if ($usage->hotels_used + $count > $limit) {
            throw SubscriptionException::hotelLimitExceeded($limit);
        }

        return true;
    }

    /**
     * Validate staff limit
     */
    public function validateStaffLimit(int $organizationId, int $count = 1): bool
    {
        $subscription = $this->getActiveSubscription($organizationId);
        
        if (!$subscription || !$subscription->isActive()) {
            throw SubscriptionException::noActiveSubscription($organizationId);
        }

        $usage = $subscription->usage;
        $limit = $subscription->plan->staff_limit;

        if ($usage->staff_used + $count > $limit) {
            throw SubscriptionException::staffLimitExceeded($limit);
        }

        return true;
    }

    /**
     * Validate booking limit
     */
    public function validateBookingLimit(int $organizationId, int $count = 1): bool
    {
        $subscription = $this->getActiveSubscription($organizationId);
        
        if (!$subscription || !$subscription->isActive()) {
            throw SubscriptionException::noActiveSubscription($organizationId);
        }

        $usage = $subscription->usage;
        $limit = $subscription->plan->booking_limit;

        if ($usage->bookings_used + $count > $limit) {
            throw SubscriptionException::bookingLimitExceeded($limit);
        }

        return true;
    }

    /**
     * Create usage record for new subscription
     */
    private function createUsageRecord(Subscription $subscription): SubscriptionUsage
    {
        return $this->usageRepo->create([
            'organization_id' => $subscription->organization_id,
            'subscription_id' => $subscription->id,
            'hotels_used' => 0,
            'staff_used' => 0,
            'rooms_used' => 0,
            'bookings_used' => 0,
            'storage_used' => 0,
            'usage_period_start' => now(),
            'usage_period_end' => now()->addMonth(),
        ]);
    }

    /**
     * Record subscription history
     */
    private function recordHistory(
        Subscription $subscription,
        SubscriptionAction $action,
        ?int $oldPlanId = null,
        ?int $newPlanId = null,
        ?string $description = null,
        ?int $changedBy = null,
    ): SubscriptionHistory {
        return SubscriptionHistory::create([
            'organization_id' => $subscription->organization_id,
            'subscription_id' => $subscription->id,
            'old_plan_id' => $oldPlanId,
            'new_plan_id' => $newPlanId,
            'action' => $action,
            'description' => $description,
            'changed_by' => $changedBy,
        ]);
    }

    /**
     * Clear subscription cache
     */
    private function clearSubscriptionCache(int $organizationId): void
    {
        Cache::forget("subscription.org.{$organizationId}");
    }
}
