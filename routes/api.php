<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        require __DIR__.'/api/v1/auth.php';
        require __DIR__.'/api/v1/super-admin.php';
        require __DIR__.'/api/v1/hotel-admin.php';
        require __DIR__.'/api/v1/hotel-manager.php';
    });
