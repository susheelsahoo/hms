<?php

namespace Modules\Subscription\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'organization_id' => $this->organization_id,
            'subscription_id' => $this->subscription_id,
            'hotels' => [
                'used' => $this->hotels_used,
                'limit' => $this->subscription->plan->hotel_limit,
                'percentage' => round($this->getUsagePercentage('hotels'), 2),
                'remaining' => $this->getRemainingQuota('hotels'),
            ],
            'staff' => [
                'used' => $this->staff_used,
                'limit' => $this->subscription->plan->staff_limit,
                'percentage' => round($this->getUsagePercentage('staff'), 2),
                'remaining' => $this->getRemainingQuota('staff'),
            ],
            'rooms' => [
                'used' => $this->rooms_used,
                'limit' => $this->subscription->plan->room_limit,
                'percentage' => round($this->getUsagePercentage('rooms'), 2),
                'remaining' => $this->getRemainingQuota('rooms'),
            ],
            'bookings' => [
                'used' => $this->bookings_used,
                'limit' => $this->subscription->plan->booking_limit,
                'percentage' => round($this->getUsagePercentage('bookings'), 2),
                'remaining' => $this->getRemainingQuota('bookings'),
            ],
            'storage' => [
                'used' => $this->storage_used,
                'limit' => $this->subscription->plan->storage_limit,
                'percentage' => round($this->getUsagePercentage('storage'), 2),
                'remaining' => $this->getRemainingQuota('storage'),
            ],
            'period' => [
                'start' => $this->usage_period_start->toIso8601String(),
                'end' => $this->usage_period_end->toIso8601String(),
            ],
        ];
    }
}
