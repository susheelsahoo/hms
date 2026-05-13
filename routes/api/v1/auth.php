<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function (): void {
    Route::get('health', fn (): array => ['module' => 'auth', 'status' => 'ok'])->name('health');
});
