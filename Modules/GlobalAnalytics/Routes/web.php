<?php

use Illuminate\Support\Facades\Route;
use Modules\GlobalAnalytics\Controllers\Web\GlobalAnalyticsDashboardController;

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin/global-analytics')
    ->name('super-admin.global-analytics.')
    ->group(function (): void {
        Route::get('/', GlobalAnalyticsDashboardController::class)->name('dashboard');
    });
