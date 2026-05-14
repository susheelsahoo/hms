<?php

namespace Modules\GlobalAnalytics\Enums;

enum AnalyticsPeriod: string
{
    case DAILY = 'daily';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function days(): int
    {
        return match ($this) {
            self::DAILY => 1,
            self::MONTHLY => 30,
            self::YEARLY => 365,
        };
    }
}
