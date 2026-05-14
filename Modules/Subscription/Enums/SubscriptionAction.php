<?php

namespace Modules\Subscription\Enums;

enum SubscriptionAction: string
{
    case CREATED = 'created';
    case UPGRADE = 'upgrade';
    case DOWNGRADE = 'downgrade';
    case RENEWAL = 'renewal';
    case CANCELLATION = 'cancellation';
    case REACTIVATION = 'reactivation';
    case SUSPENSION = 'suspension';
    case EXPIRATION = 'expiration';
    case TRIAL_STARTED = 'trial_started';
    case TRIAL_ENDED = 'trial_ended';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Subscription Created',
            self::UPGRADE => 'Plan Upgraded',
            self::DOWNGRADE => 'Plan Downgraded',
            self::RENEWAL => 'Subscription Renewed',
            self::CANCELLATION => 'Subscription Cancelled',
            self::REACTIVATION => 'Subscription Reactivated',
            self::SUSPENSION => 'Subscription Suspended',
            self::EXPIRATION => 'Subscription Expired',
            self::TRIAL_STARTED => 'Trial Started',
            self::TRIAL_ENDED => 'Trial Ended',
        };
    }
}
