<?php

use Illuminate\Support\Facades\Route;

Route::prefix('super-admin')->name('super-admin.')->group(function (): void {
    Route::get('health', fn (): array => ['module' => 'super-admin', 'status' => 'ok'])->name('health');
});
