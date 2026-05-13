<?php

use Illuminate\Support\Facades\Route;

Route::prefix('hotel-manager')->name('hotel-manager.')->group(function (): void {
    Route::get('health', fn (): array => ['module' => 'hotel-manager', 'status' => 'ok'])->name('health');
});
