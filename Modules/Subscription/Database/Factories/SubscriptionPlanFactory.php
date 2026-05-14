<?php

namespace Modules\Subscription\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'description' => $this->faker->paragraph(),
            'price_monthly' => $this->faker->numberBetween(2999, 19999) / 100,
            'price_yearly' => $this->faker->numberBetween(29990, 199990) / 100,
            'hotel_limit' => $this->faker->numberBetween(1, 100),
            'staff_limit' => $this->faker->numberBetween(5, 500),
            'room_limit' => $this->faker->numberBetween(50, 10000),
            'booking_limit' => $this->faker->numberBetween(100, 100000),
            'storage_limit' => $this->faker->numberBetween(10, 1000),
            'features' => [],
            'is_trial' => false,
            'trial_days' => 14,
            'is_active' => true,
            'metadata' => [],
        ];
    }

    public function trial(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_trial' => true,
                'price_monthly' => 0,
                'price_yearly' => 0,
            ];
        });
    }

    public function premium(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Premium Plan',
                'slug' => 'premium',
                'hotel_limit' => 10,
                'staff_limit' => 100,
                'room_limit' => 5000,
                'booking_limit' => 50000,
            ];
        });
    }
}
