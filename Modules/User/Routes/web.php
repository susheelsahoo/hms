<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Controllers\Web\PermissionController;
use Modules\User\Controllers\Web\RoleController;
use Modules\User\Controllers\Web\UserController;

Route::middleware(['auth', 'permission:manage_staff'])
    ->prefix('user-management')
    ->name('user-management.')
    ->group(function (): void {
        Route::resource('permissions', PermissionController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });
