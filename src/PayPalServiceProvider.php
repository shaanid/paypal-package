<?php

namespace Shaanid\PayPal;

use Illuminate\Support\ServiceProvider;

class PayPalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/paypal.php', 'paypal');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'paypal');

        // Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/paypal.php' => config_path('paypal.php'),
            ], 'paypal-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/paypal'),
            ], 'paypal-views');
        }
    }
}
