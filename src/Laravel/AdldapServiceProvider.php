<?php

namespace Adldap\Laravel;

use Adldap\Adldap;

// In Laravel, ServiceProvider exists in Illuminate\Support\ServiceProvider.
// Using class_alias or duck typing lets this work in Laravel without hard-requiring Illuminate in composer.json
if (class_exists('Illuminate\Support\ServiceProvider')) {
    class BaseProvider extends \Illuminate\Support\ServiceProvider {}
} else {
    class BaseProvider {
        protected $app;
        public function __construct($app = null) { $this->app = $app; }
        public function publishes(array $paths, $groups = null) {}
        public function mergeConfigFrom($path, $key) {}
    }
}

class AdldapServiceProvider extends BaseProvider
{
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/adldap.php' => function_exists('config_path')
                ? config_path('adldap.php')
                : base_path('config/adldap.php'),
        ], 'adldap-config');
    }

    /**
     * Register package services and container binding.
     */
    public function register(): void
    {
        if (function_exists('config_path')) {
            $this->mergeConfigFrom(__DIR__ . '/../../config/adldap.php', 'adldap');
        }

        $this->app->singleton(Adldap::class, function ($app) {
            $config = $app['config']->get('adldap', []);
            return new Adldap($config);
        });

        $this->app->alias(Adldap::class, 'adldap');
    }
}
