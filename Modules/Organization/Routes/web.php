<?php

use Illuminate\Support\Facades\Route;
use Modules\Organization\Controllers\Web\OrganizationController;

Route::middleware(['auth', 'role:super_admin'])->group(function (): void {
    Route::resource('super-admin/organizations', OrganizationController::class)
        ->names('super-admin.organizations')
        ->except(['show']);
});
