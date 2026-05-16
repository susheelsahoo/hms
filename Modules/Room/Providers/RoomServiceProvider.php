<?php

namespace Modules\Room\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RoomServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $viewPath = base_path('Modules/Room/Resources/views');

        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, 'room');
        }

        $routeFile = base_path('Modules/Room/Routes/web.php');

        if (file_exists($routeFile)) {
            Route::middleware(['web'])
                ->group($routeFile);
        }
    }
}
