<?php

namespace Modules\Subscription\Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Subscription\Enums\BillingCycle;
use Modules\Subscription\Enums\SubscriptionStatus;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $startDate = Carbon::instance($this->faker->dateTimeBetween('-30 days', 'now'));
        $billingCycle = $this->faker->randomElement(BillingCycle::cases());
        $days = $billingCycle->daysInCycle();

        return [
            'organization_id' => Organization::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'status' => SubscriptionStatus::ACTIVE,
            'billing_cycle' => $billingCycle->value,
            'starts_at' => $startDate,
            'ends_at' => $startDate->copy()->addDays($days),
            'trial_ends_at' => null,
            'grace_ends_at' => null,
            'cancelled_at' => null,
            'renewal_at' => $startDate->copy()->addDays($days),
            'amount' => $this->faker->numberBetween(2999, 19999) / 100,
            'currency' => 'USD',
            'auto_renew' => true,
            'metadata' => [],
        ];
    }

    public function trial(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => SubscriptionStatus::TRIAL,
                'trial_ends_at' => now()->addDays(14),
            ];
        });
    }

    public function expired(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => SubscriptionStatus::EXPIRED,
                'ends_at' => now()->subDay(),
            ];
        });
    }

    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => SubscriptionStatus::CANCELLED,
                'cancelled_at' => now(),
                'auto_renew' => false,
            ];
        });
    }
}
