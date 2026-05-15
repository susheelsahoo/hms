<?php

namespace Modules\Hotel\Services;

use Modules\Hotel\Models\Hotel;
use Modules\User\Models\User;

class HotelService
{
    /**
     * Get hotels accessible by the current user based on their role
     */
    public function getUserHotels(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->isSuperAdmin()) {
            // Super admin can see all hotels
            return Hotel::query()
                ->with('organization')
                ->orderBy('name')
                ->get();
        }

        if ($user->isOrganizationOwner()) {
            // Organization owner sees all hotels in their organization
            return Hotel::query()
                ->where('organization_id', $user->organization_id)
                ->orderBy('name')
                ->get();
        }

        // Hotel managers/staff see only hotels they're assigned to
        return $user->hotels()
            ->where('access_type', '!=', 'staff')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the user's primary or default hotel
     */
    public function getDefaultHotel(User $user): ?Hotel
    {
        // First try to get primary hotel
        $primary = $user->hotels()
            ->wherePivot('is_primary', true)
            ->first();

        if ($primary) {
            return $primary;
        }

        // Otherwise get first hotel
        return $this->getUserHotels($user)->first();
    }

    /**
     * Check if user can access a specific hotel
     */
    public function canAccessHotel(User $user, Hotel $hotel): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isOrganizationOwner()) {
            return $user->organization_id === $hotel->organization_id;
        }

        return $user->hotels()
            ->where('hotels.id', $hotel->id)
            ->exists();
    }

    /**
     * Get user's role/access type for a specific hotel
     */
    public function getUserHotelAccessType(User $user, Hotel $hotel): ?string
    {
        if ($user->isSuperAdmin()) {
            return 'owner';
        }

        return $user->hotels()
            ->where('hotels.id', $hotel->id)
            ->value('access_type');
    }
}
