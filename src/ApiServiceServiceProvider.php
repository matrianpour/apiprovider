<?php

namespace Mtrn\ApiService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Mtrn\ApiService\ApiProviders\ApiProvider;

class ApiServiceServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
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
        $this->mergeConfigFrom(__DIR__.'/../config/apiservice.php', 'apiservice');

        // Register the service the package provides.
        $this->app->singleton('apiservice', function ($app) {
            return new ApiService;
        });
        
        $this->app->bind(ApiProvider::class, function ($app, $params) {
            $apiName =$params['apiName'];
            $providerName = Str::studly($apiName).'ApiProvider';
            $pathToProvider = "Mtrn\\ApiService\\ApiProviders\\".$providerName;
            return $app->make($pathToProvider);
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['apiservice'];
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
            __DIR__.'/../config/apiservice.php' => config_path('apiservice.php'),
        ], 'apiservice.config');
    }
}
