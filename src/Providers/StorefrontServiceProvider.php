<?php

namespace Ingenius\Storefront\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Services\FeatureManager;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Core\Traits\RegistersMigrations;
use Ingenius\Storefront\Features\ListShopProductsFeature;

class StorefrontServiceProvider extends ServiceProvider
{
    use RegistersMigrations, RegistersConfigurations;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/storefront.php', 'storefront');

        // Register configuration with the registry
        $this->registerConfig(__DIR__ . '/../../config/storefront.php', 'storefront', 'storefront');

        // Register the route service provider
        $this->app->register(RouteServiceProvider::class);

        $this->app->afterResolving(FeatureManager::class, function (FeatureManager $manager) {
            $manager->register(new ListShopProductsFeature());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register migrations with the registry
        $this->registerMigrations(__DIR__ . '/../../database/migrations', 'storefront');

        // Check if there's a tenant migrations directory and register it
        $tenantMigrationsPath = __DIR__ . '/../../database/migrations/tenant';
        if (is_dir($tenantMigrationsPath)) {
            $this->registerTenantMigrations($tenantMigrationsPath, 'storefront');
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/storefront.php' => config_path('storefront.php'),
        ], 'storefront-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'storefront-migrations');
    }
}
