<?php

namespace Modules\Subscription\Enums;

enum BillingCycle: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }

    public function daysInCycle(): int
    {
        return match ($this) {
            self::MONTHLY => 30,
            self::YEARLY => 365,
        };
    }
}
