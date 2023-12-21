<?php

namespace Tahseen9\LaravelSupervisorManager;

use Illuminate\Support\ServiceProvider;

class LaravelSupervisorManagerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tahseen9');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'tahseen9');
         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-supervisor-manager.php', 'laravel-supervisor-manager');

        // Register the service the package provides.
        $this->app->singleton('laravel-supervisor-manager', function ($app) {
            return new LaravelSupervisorManager;
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-supervisor-manager'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravel-supervisor-manager.php' => config_path('laravel-supervisor-manager.php'),
        ], 'laravel-supervisor-manager.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/tahseen9'),
        ], 'laravel-supervisor-manager.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/tahseen9'),
        ], 'laravel-supervisor-manager.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/tahseen9'),
        ], 'laravel-supervisor-manager.lang');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
