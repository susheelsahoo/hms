<?php

use Illuminate\Support\Facades\Route;
use Modules\Room\Controllers\Web\RateTypeController;
use Modules\Room\Controllers\Web\RoomController;
use Modules\Room\Controllers\Web\RoomStatusController;
use Modules\Room\Controllers\Web\RoomTypeController;

Route::middleware(['auth', 'role:organization_owner,hotel_admin,hotel_manager'])
    ->prefix('room-management')
    ->name('room-management.')
    ->group(function (): void {
        Route::resource('rate-types', RateTypeController::class)->except(['show']);
        Route::resource('room-types', RoomTypeController::class)->except(['show']);
        Route::resource('rooms', RoomController::class)->except(['show']);
        Route::get('room-statuses', [RoomStatusController::class, 'index'])->name('room-statuses.index');
        Route::patch('room-statuses/{room}', [RoomStatusController::class, 'update'])->name('room-statuses.update');
    });
