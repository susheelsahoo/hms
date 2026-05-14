<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\SubscriptionFeature;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Trial Plan
        $trialPlan = SubscriptionPlan::firstOrCreate([
            'slug' => 'trial',
        ], [
            'name' => 'Trial',
            'description' => '14-day free trial to experience all premium features',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'hotel_limit' => 1,
            'staff_limit' => 5,
            'room_limit' => 50,
            'booking_limit' => 500,
            'storage_limit' => 5,
            'is_trial' => true,
            'trial_days' => 14,
            'is_active' => true,
        ]);

        // Basic Plan
        $basicPlan = SubscriptionPlan::firstOrCreate([
            'slug' => 'basic',
        ], [
            'name' => 'Basic',
            'description' => 'Perfect for small hotels',
            'price_monthly' => 29.99,
            'price_yearly' => 299.90,
            'hotel_limit' => 1,
            'staff_limit' => 10,
            'room_limit' => 100,
            'booking_limit' => 2000,
            'storage_limit' => 20,
            'is_trial' => false,
            'is_active' => true,
        ]);

        // Professional Plan
        $proPlan = SubscriptionPlan::firstOrCreate([
            'slug' => 'professional',
        ], [
            'name' => 'Professional',
            'description' => 'For growing hotel groups',
            'price_monthly' => 79.99,
            'price_yearly' => 799.90,
            'hotel_limit' => 5,
            'staff_limit' => 50,
            'room_limit' => 1000,
            'booking_limit' => 10000,
            'storage_limit' => 100,
            'is_trial' => false,
            'is_active' => true,
        ]);

        // Enterprise Plan
        $enterprisePlan = SubscriptionPlan::firstOrCreate([
            'slug' => 'enterprise',
        ], [
            'name' => 'Enterprise',
            'description' => 'For large-scale hotel operations',
            'price_monthly' => 199.99,
            'price_yearly' => 1999.90,
            'hotel_limit' => 99999,
            'staff_limit' => 99999,
            'room_limit' => 99999,
            'booking_limit' => 99999,
            'storage_limit' => 1000,
            'is_trial' => false,
            'is_active' => true,
        ]);

        // Add features to plans
        $features = [
            ['key' => 'advanced_reporting', 'name' => 'Advanced Reporting'],
            ['key' => 'api_access', 'name' => 'API Access'],
            ['key' => 'custom_branding', 'name' => 'Custom Branding'],
            ['key' => 'priority_support', 'name' => 'Priority Support'],
            ['key' => 'white_label', 'name' => 'White Label'],
            ['key' => 'payment_processing', 'name' => 'Payment Processing'],
        ];

        foreach ($features as $feature) {
            foreach ([$trialPlan, $basicPlan, $proPlan, $enterprisePlan] as $plan) {
                SubscriptionFeature::firstOrCreate([
                    'subscription_plan_id' => $plan->id,
                    'feature_key' => $feature['key'],
                ], [
                    'feature_name' => $feature['name'],
                    'is_included' => $plan->slug !== 'trial' || in_array($feature['key'], ['advanced_reporting', 'api_access']),
                    'description' => "Access to {$feature['name']} feature",
                ]);
            }
        }
    }
}
