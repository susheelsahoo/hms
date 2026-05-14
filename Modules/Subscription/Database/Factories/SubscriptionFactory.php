<?php

namespace Modules\Subscription\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Enums\SubscriptionStatus;
use Modules\Subscription\Enums\BillingCycle;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $billingCycle = $this->faker->randomElement(BillingCycle::cases());
        $days = $billingCycle->daysInCycle();

        return [
            'organization_id' => \App\Models\Organization::factory(),
            'subscription_plan_id' => \Modules\Subscription\Models\SubscriptionPlan::factory(),
            'status' => SubscriptionStatus::ACTIVE,
            'billing_cycle' => $billingCycle->value,
            'starts_at' => $startDate,
            'ends_at' => (new \DateTime($startDate))->modify("+{$days} days"),
            'trial_ends_at' => null,
            'grace_ends_at' => null,
            'cancelled_at' => null,
            'renewal_at' => (new \DateTime($startDate))->modify("+{$days} days"),
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
