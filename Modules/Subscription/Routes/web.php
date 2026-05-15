<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Controllers\Web\SubscriptionPlanController;

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin/subscription-plans')
    ->name('super-admin.subscription-plans.')
    ->group(function (): void {
        Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
        Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
        Route::post('/', [SubscriptionPlanController::class, 'store'])->name('store');
        Route::get('/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])->name('edit');
        Route::put('/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])->name('update');
        Route::patch('{subscriptionPlan}/status', [SubscriptionPlanController::class, 'updateStatus'])->name('status.update');
    });
