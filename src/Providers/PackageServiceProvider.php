<?php

namespace JDD\Api\Providers;

use Illuminate\Support\ServiceProvider;
use JDD\Api\Console\Commands\UpdatePackage;

class PackageServiceProvider extends ServiceProvider
{
    const PluginName = 'coredump/jdd-api';

    /**
     * If your plugin will provide any services, you can register them here.
     * See: https://laravel.com/docs/5.6/providers#the-register-method
     */
    public function register()
    {
        // Nothing is registered at this time
    }

    /**
     * After all service provider's register methods have been called, your boot method
     * will be called. You can perform any initialization code that is dependent on
     * other service providers at this time.  We've included some example behavior
     * to get you started.
     *
     * See: https://laravel.com/docs/5.6/providers#the-boot-method
     */
    public function boot()
    {
        // Register artisan commands
        $this->commands([UpdatePackage::class]);
        $this->publishes([
            __DIR__ . '/../../config/jsonapi.php' => config_path('jsonapi.php'),
        ]);
        // Publish assets
        $this->publishes([
            __DIR__ . '/../../dist' => public_path('modules/' . self::PluginName),
        ], self::PluginName . '/assets');
        app('config')->push('plugins.javascript_before', '/modules/' . self::PluginName . '/index.umd.min.js');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
