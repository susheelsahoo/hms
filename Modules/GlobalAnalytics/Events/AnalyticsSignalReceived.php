<?php

namespace Modules\GlobalAnalytics\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class AnalyticsSignalReceived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $signal,
        public readonly Carbon $analyticsDate,
        public readonly array $payload = [],
    ) {}
}
