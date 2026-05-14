<?php

namespace Modules\Subscription\Enums;

enum SubscriptionStatus: string
{
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case PAST_DUE = 'past_due';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::TRIAL => 'Trial',
            self::ACTIVE => 'Active',
            self::PAST_DUE => 'Past Due',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
            self::SUSPENDED => 'Suspended',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TRIAL => 'blue',
            self::ACTIVE => 'green',
            self::PAST_DUE => 'orange',
            self::EXPIRED => 'red',
            self::CANCELLED => 'gray',
            self::SUSPENDED => 'red',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::TRIAL, self::ACTIVE]);
    }

    public function isExpired(): bool
    {
        return in_array($this, [self::EXPIRED, self::CANCELLED, self::SUSPENDED]);
    }
}
