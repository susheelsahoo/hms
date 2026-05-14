<?php

namespace Modules\Subscription\Services;

use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionUsage;
use Modules\Subscription\Exceptions\SubscriptionException;

class LimitValidator
{
    /**
     * Validate and track hotel creation
     */
    public function validateAndTrackHotel(int $organizationId, int $count = 1): void
    {
        $subscription = $this->getSubscription($organizationId);
        $usage = $subscription->usage;

        if ($usage->hotels_used + $count > $subscription->plan->hotel_limit) {
            throw SubscriptionException::hotelLimitExceeded($subscription->plan->hotel_limit);
        }

        $usage->updateUsage('hotels', $count);
    }

    /**
     * Validate and track staff creation
     */
    public function validateAndTrackStaff(int $organizationId, int $count = 1): void
    {
        $subscription = $this->getSubscription($organizationId);
        $usage = $subscription->usage;

        if ($usage->staff_used + $count > $subscription->plan->staff_limit) {
            throw SubscriptionException::staffLimitExceeded($subscription->plan->staff_limit);
        }

        $usage->updateUsage('staff', $count);
    }

    /**
     * Validate and track room creation
     */
    public function validateAndTrackRooms(int $organizationId, int $count = 1): void
    {
        $subscription = $this->getSubscription($organizationId);
        $usage = $subscription->usage;

        if ($usage->rooms_used + $count > $subscription->plan->room_limit) {
            throw SubscriptionException::roomLimitExceeded($subscription->plan->room_limit);
        }

        $usage->updateUsage('rooms', $count);
    }

    /**
     * Validate and track booking
     */
    public function validateAndTrackBooking(int $organizationId, int $count = 1): void
    {
        $subscription = $this->getSubscription($organizationId);
        $usage = $subscription->usage;

        if ($usage->bookings_used + $count > $subscription->plan->booking_limit) {
            throw SubscriptionException::bookingLimitExceeded($subscription->plan->booking_limit);
        }

        $usage->updateUsage('bookings', $count);
    }

    /**
     * Get usage statistics
     */
    public function getUsageStats(int $organizationId): array
    {
        $subscription = $this->getSubscription($organizationId);
        $usage = $subscription->usage;
        $plan = $subscription->plan;

        return [
            'hotels' => [
                'used' => $usage->hotels_used,
                'limit' => $plan->hotel_limit,
                'percentage' => $usage->getUsagePercentage('hotels'),
                'remaining' => $usage->getRemainingQuota('hotels'),
            ],
            'staff' => [
                'used' => $usage->staff_used,
                'limit' => $plan->staff_limit,
                'percentage' => $usage->getUsagePercentage('staff'),
                'remaining' => $usage->getRemainingQuota('staff'),
            ],
            'rooms' => [
                'used' => $usage->rooms_used,
                'limit' => $plan->room_limit,
                'percentage' => $usage->getUsagePercentage('rooms'),
                'remaining' => $usage->getRemainingQuota('rooms'),
            ],
            'bookings' => [
                'used' => $usage->bookings_used,
                'limit' => $plan->booking_limit,
                'percentage' => $usage->getUsagePercentage('bookings'),
                'remaining' => $usage->getRemainingQuota('bookings'),
            ],
        ];
    }

    /**
     * Get subscription
     */
    private function getSubscription(int $organizationId): Subscription
    {
        $subscription = Subscription::where('organization_id', $organizationId)
            ->with('usage', 'plan')
            ->first();

        if (!$subscription) {
            throw SubscriptionException::noActiveSubscription($organizationId);
        }

        if (!$subscription->isActive()) {
            throw SubscriptionException::subscriptionExpired();
        }

        return $subscription;
    }
}
