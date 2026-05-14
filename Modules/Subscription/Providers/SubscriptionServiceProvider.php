<?php

namespace Modules\Subscription\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Subscription\Events\InvoiceGenerated;
use Modules\Subscription\Events\PlanUpgraded;
use Modules\Subscription\Events\SubscriptionCancelled;
use Modules\Subscription\Events\SubscriptionCreated;
use Modules\Subscription\Events\SubscriptionExpired;
use Modules\Subscription\Interfaces\SubscriptionPlanRepositoryInterface;
use Modules\Subscription\Interfaces\SubscriptionRepositoryInterface;
use Modules\Subscription\Interfaces\SubscriptionUsageRepositoryInterface;
use Modules\Subscription\Listeners\LogPlanUpgrade;
use Modules\Subscription\Listeners\LogSubscriptionCancellation;
use Modules\Subscription\Listeners\NotifySubscriptionExpired;
use Modules\Subscription\Listeners\SendInvoiceNotification;
use Modules\Subscription\Listeners\SendSubscriptionCreatedNotification;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionInvoice;
use Modules\Subscription\Policies\SubscriptionInvoicePolicy;
use Modules\Subscription\Policies\SubscriptionPolicy;
use Modules\Subscription\Repositories\SubscriptionPlanRepository;
use Modules\Subscription\Repositories\SubscriptionRepository;
use Modules\Subscription\Repositories\SubscriptionUsageRepository;
use Modules\Subscription\Services\InvoiceService;
use Modules\Subscription\Services\LimitValidator;
use Modules\Subscription\Services\SubscriptionService;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register repositories
        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            SubscriptionRepository::class
        );
        $this->app->bind(
            SubscriptionPlanRepositoryInterface::class,
            SubscriptionPlanRepository::class
        );
        $this->app->bind(
            SubscriptionUsageRepositoryInterface::class,
            SubscriptionUsageRepository::class
        );

        // Register services as singletons
        $this->app->singleton(SubscriptionService::class, function ($app) {
            return new SubscriptionService(
                new SubscriptionRepository,
                new SubscriptionPlanRepository,
                new SubscriptionUsageRepository,
                new InvoiceService,
            );
        });

        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(LimitValidator::class);
    }

    public function boot(): void
    {
        $this->registerViews();

        $this->registerWebRoutes();

        // Register migrations
        $this->registerMigrations();

        // Register seeders
        $this->registerSeeders();

        // Register policies
        $this->registerPolicies();

        // Register event listeners
        $this->registerEventListeners();

        // Register console commands
        $this->registerCommands();

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config' => config_path('subscription'),
        ], 'subscription-config');
    }

    private function registerViews(): void
    {
        $viewPath = base_path('Modules/Subscription/Resources/views');

        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, 'subscription');
        }
    }

    private function registerWebRoutes(): void
    {
        $routeFile = base_path('Modules/Subscription/Routes/web.php');

        if (file_exists($routeFile)) {
            Route::middleware(['web'])
                ->group($routeFile);
        }
    }

    private function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    private function registerSeeders(): void
    {
        // Seeders can be called manually with artisan command
    }

    private function registerPolicies(): void
    {
        Gate::policy(Subscription::class, SubscriptionPolicy::class);
        Gate::policy(SubscriptionInvoice::class, SubscriptionInvoicePolicy::class);
    }

    private function registerEventListeners(): void
    {
        // Event listener mapping
        $this->app['events']->listen(
            SubscriptionCreated::class,
            SendSubscriptionCreatedNotification::class
        );

        $this->app['events']->listen(
            SubscriptionExpired::class,
            NotifySubscriptionExpired::class
        );

        $this->app['events']->listen(
            InvoiceGenerated::class,
            SendInvoiceNotification::class
        );

        $this->app['events']->listen(
            PlanUpgraded::class,
            LogPlanUpgrade::class
        );

        $this->app['events']->listen(
            SubscriptionCancelled::class,
            LogSubscriptionCancellation::class
        );
    }

    private function registerCommands(): void
    {
        // Register artisan commands if any
    }

    public function provides(): array
    {
        return [
            SubscriptionService::class,
            InvoiceService::class,
            LimitValidator::class,
            SubscriptionRepositoryInterface::class,
            SubscriptionPlanRepositoryInterface::class,
            SubscriptionUsageRepositoryInterface::class,
        ];
    }
}
