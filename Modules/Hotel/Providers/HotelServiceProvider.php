<?php

namespace Modules\Hotel\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class HotelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $viewPath = base_path('Modules/Hotel/Resources/views');

        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, 'hotel');
        }

        $routeFile = base_path('Modules/Hotel/Routes/web.php');

        if (file_exists($routeFile)) {
            Route::middleware(['web'])
                ->group($routeFile);
        }
    }
}
