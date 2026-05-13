<?php

namespace Modules\Booking\Controllers\Api\V1;

final class BookingController
{
    public function health(): array
    {
        return [
            'module' => 'booking',
            'status' => 'ok',
        ];
    }
}
