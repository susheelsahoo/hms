<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->enabledModules() as $module) {
            $provider = "Modules\\{$module}\\Providers\\{$module}ServiceProvider";

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    public function boot(): void
    {
        foreach ($this->enabledModules() as $module) {
            $this->loadModuleApiRoutes($module);
            $this->loadModuleMigrations($module);
        }
    }

    /**
     * @return array<int, string>
     */
    private function enabledModules(): array
    {
        return config('modules.enabled', []);
    }

    private function loadModuleApiRoutes(string $module): void
    {
        $routeFile = base_path("Modules/{$module}/Routes/api.php");

        if (! file_exists($routeFile)) {
            return;
        }

        Route::middleware(['api'])
            ->prefix(config('modules.api_prefix', 'api/v1'))
            ->name('api.v1.'.str($module)->kebab()->toString().'.')
            ->group($routeFile);
    }

    private function loadModuleMigrations(string $module): void
    {
        $migrationPath = base_path("Modules/{$module}/Database/Migrations");

        if (is_dir($migrationPath)) {
            $this->loadMigrationsFrom($migrationPath);
        }
    }
}
