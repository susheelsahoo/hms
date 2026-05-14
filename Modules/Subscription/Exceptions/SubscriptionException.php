<?php

namespace Modules\Subscription\Exceptions;

use Exception;

class SubscriptionException extends Exception
{
    public static function organizationAlreadySubscribed(int $organizationId): self
    {
        return new self("Organization {$organizationId} already has an active subscription.");
    }

    public static function subscriptionNotFound(int $subscriptionId): self
    {
        return new self("Subscription {$subscriptionId} not found.");
    }

    public static function invalidStatusTransition(string $from, string $to): self
    {
        return new self("Cannot transition subscription status from '{$from}' to '{$to}'.");
    }

    public static function cannotCancelExpiredSubscription(): self
    {
        return new self("Cannot cancel an already expired subscription.");
    }

    public static function hotelLimitExceeded(int $limit): self
    {
        return new self("Hotel limit of {$limit} has been exceeded.");
    }

    public static function staffLimitExceeded(int $limit): self
    {
        return new self("Staff limit of {$limit} has been exceeded.");
    }

    public static function bookingLimitExceeded(int $limit): self
    {
        return new self("Booking limit of {$limit} for this period has been exceeded.");
    }

    public static function roomLimitExceeded(int $limit): self
    {
        return new self("Room limit of {$limit} has been exceeded.");
    }

    public static function storageLimitExceeded(int $limit): self
    {
        return new self("Storage limit of {$limit} GB has been exceeded.");
    }

    public static function featureNotAvailable(string $feature): self
    {
        return new self("Feature '{$feature}' is not available in this subscription plan.");
    }

    public static function subscriptionExpired(): self
    {
        return new self("Subscription has expired.");
    }

    public static function subscriptionSuspended(): self
    {
        return new self("Subscription has been suspended.");
    }

    public static function cannotUpgradeSamePlan(): self
    {
        return new self("Cannot upgrade to the same plan.");
    }

    public static function cannotDowngradeToExpiredPlan(): self
    {
        return new self("Cannot downgrade to a plan with no availability.");
    }

    public static function invalidBillingCycle(string $cycle): self
    {
        return new self("Invalid billing cycle: {$cycle}.");
    }

    public static function planNotFound(int $planId): self
    {
        return new self("Subscription plan {$planId} not found.");
    }

    public static function noActiveSubscription(int $organizationId): self
    {
        return new self("Organization {$organizationId} has no active subscription.");
    }
}
