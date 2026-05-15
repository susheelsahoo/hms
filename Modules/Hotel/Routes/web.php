<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Controllers\Web\HotelController;

Route::middleware(['auth', 'role:super_admin'])->group(function (): void {
    Route::resource('super-admin/organizations/{organization}/hotels', HotelController::class)
        ->parameters(['hotels' => 'hotel'])
        ->names('super-admin.organizations.hotels')
        ->except(['show']);
});

// Organization Owner and Hotel Manager routes
Route::middleware(['auth'])->group(function (): void {
    Route::get('/hotels', [HotelController::class, 'listUserHotels'])->name('hotels.list');
    Route::post('/hotels/{hotel}/select', [HotelController::class, 'selectHotel'])->name('hotels.select');
});
