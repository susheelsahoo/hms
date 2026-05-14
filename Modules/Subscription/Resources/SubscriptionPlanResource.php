<?php

namespace Modules\Subscription\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_monthly' => (float) $this->price_monthly,
            'price_yearly' => (float) $this->price_yearly,
            'yearly_savings' => $this->getYearlySavings(),
            'savings_percentage' => round($this->getSavingsPercentage(), 2),
            'limits' => [
                'hotels' => $this->hotel_limit,
                'staff' => $this->staff_limit,
                'rooms' => $this->room_limit,
                'bookings' => $this->booking_limit,
                'storage' => $this->storage_limit,
            ],
            'is_trial' => $this->is_trial,
            'trial_days' => $this->trial_days,
            'is_active' => $this->is_active,
            'features' => $this->whenLoaded('features', function () {
                return $this->getRelation('features')->map(fn ($f) => [
                    'key' => $f->feature_key,
                    'name' => $f->feature_name,
                    'included' => $f->is_included,
                ]);
            }),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
