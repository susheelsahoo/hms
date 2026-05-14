<?php

namespace Modules\Subscription\Policies;

use App\Models\User;
use Modules\Subscription\Models\Subscription;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->organization_id !== null;
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $user->organization_id === $subscription->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $user->organization_id === $subscription->organization_id && $user->isAdmin();
    }

    public function upgrade(User $user, Subscription $subscription): bool
    {
        return $user->organization_id === $subscription->organization_id && $user->isAdmin();
    }

    public function downgrade(User $user, Subscription $subscription): bool
    {
        return $user->organization_id === $subscription->organization_id && $user->isAdmin();
    }

    public function cancel(User $user, Subscription $subscription): bool
    {
        return $user->organization_id === $subscription->organization_id && $user->isAdmin();
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return false; // Subscriptions should not be deleted, only cancelled
    }
}
