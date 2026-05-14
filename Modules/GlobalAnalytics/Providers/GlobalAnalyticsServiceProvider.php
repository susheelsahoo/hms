<?php

namespace Modules\GlobalAnalytics\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\GlobalAnalytics\Events\AnalyticsSignalReceived;
use Modules\GlobalAnalytics\Interfaces\GlobalAnalyticsRepositoryInterface;
use Modules\GlobalAnalytics\Listeners\QueueAnalyticsAggregation;
use Modules\GlobalAnalytics\Repositories\GlobalAnalyticsRepository;
use Modules\GlobalAnalytics\Services\GlobalAnalyticsAggregator;
use Modules\GlobalAnalytics\Services\GlobalAnalyticsService;

class GlobalAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GlobalAnalyticsRepositoryInterface::class, GlobalAnalyticsRepository::class);
        $this->app->singleton(GlobalAnalyticsAggregator::class);
        $this->app->singleton(GlobalAnalyticsService::class);
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerWebRoutes();

        Event::listen(AnalyticsSignalReceived::class, QueueAnalyticsAggregation::class);
    }

    private function registerViews(): void
    {
        $viewPath = base_path('Modules/GlobalAnalytics/Resources/views');

        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, 'global-analytics');
        }
    }

    private function registerWebRoutes(): void
    {
        $routeFile = base_path('Modules/GlobalAnalytics/Routes/web.php');

        if (file_exists($routeFile)) {
            Route::middleware(['web'])->group($routeFile);
        }
    }
}
