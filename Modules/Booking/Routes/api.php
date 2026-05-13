<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Controllers\Api\V1\BookingController;

Route::prefix('bookings')->name('bookings.')->group(function (): void {
    Route::get('health', [BookingController::class, 'health'])->name('health');
});
