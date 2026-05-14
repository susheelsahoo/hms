<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Controllers\SubscriptionController;
use Modules\Subscription\Controllers\SubscriptionInvoiceController;
use Modules\Subscription\Controllers\SubscriptionPlanController;

Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // Subscription Plans
    Route::prefix('subscription-plans')->group(function () {
        Route::get('/', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
        Route::get('/{id}', [SubscriptionPlanController::class, 'show'])->name('subscription-plans.show');
        Route::get('/slug/{slug}', [SubscriptionPlanController::class, 'bySlug'])->name('subscription-plans.by-slug');
        Route::post('/compare', [SubscriptionPlanController::class, 'compare'])->name('subscription-plans.compare');
    });

    // Subscriptions
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('/', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::post('/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscriptions.upgrade');
        Route::post('/downgrade', [SubscriptionController::class, 'downgrade'])->name('subscriptions.downgrade');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::get('/usage', [SubscriptionController::class, 'usage'])->name('subscriptions.usage');
    });

    // Subscription Invoices
    Route::prefix('subscription-invoices')->group(function () {
        Route::get('/', [SubscriptionInvoiceController::class, 'index'])->name('subscription-invoices.index');
        Route::get('/{invoiceNumber}', [SubscriptionInvoiceController::class, 'show'])->name('subscription-invoices.show');
        Route::get('/{invoiceNumber}/download', [SubscriptionInvoiceController::class, 'download'])->name('subscription-invoices.download');
        Route::get('/overdue', [SubscriptionInvoiceController::class, 'overdue'])->name('subscription-invoices.overdue');
    });
});
