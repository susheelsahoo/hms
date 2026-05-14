<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\Web\AuthenticatedSessionController;
use Modules\Auth\Controllers\Web\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('super-admin/dashboard', DashboardController::class)->name('super-admin.dashboard');
    Route::get('hotel-admin/dashboard', DashboardController::class)->name('hotel-admin.dashboard');
    Route::get('hotel-manager/dashboard', DashboardController::class)->name('hotel-manager.dashboard');
});
