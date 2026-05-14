<?php

namespace Modules\User\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $viewPath = base_path('Modules/User/Resources/views');

        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, 'user');
        }

        $routeFile = base_path('Modules/User/Routes/web.php');

        if (file_exists($routeFile)) {
            Route::middleware(['web'])
                ->group($routeFile);
        }
    }
}
