<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Controllers\Web\SubscriptionPlanController;

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin/subscription-plans')
    ->name('super-admin.subscription-plans.')
    ->group(function (): void {
        Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
        Route::patch('{subscriptionPlan}/status', [SubscriptionPlanController::class, 'updateStatus'])->name('status.update');
    });
