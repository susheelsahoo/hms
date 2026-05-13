<?php

use Illuminate\Support\Facades\Route;

Route::prefix('hotel-admin')->name('hotel-admin.')->group(function (): void {
    Route::get('health', fn (): array => ['module' => 'hotel-admin', 'status' => 'ok'])->name('health');
});
