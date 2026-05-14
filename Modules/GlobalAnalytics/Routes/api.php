<?php

use Illuminate\Support\Facades\Route;
use Modules\GlobalAnalytics\Controllers\Api\V1\GlobalAnalyticsController;

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('global-analytics')
    ->name('global-analytics.')
    ->group(function (): void {
        Route::get('dashboard', [GlobalAnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('bookings', [GlobalAnalyticsController::class, 'bookings'])->name('bookings');
        Route::get('revenue', [GlobalAnalyticsController::class, 'revenue'])->name('revenue');
        Route::get('subscriptions', [GlobalAnalyticsController::class, 'subscriptions'])->name('subscriptions');
        Route::get('system-health', [GlobalAnalyticsController::class, 'systemHealth'])->name('system-health');
    });
