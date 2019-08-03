<?php

namespace Leitom\Geo;

use Leitom\Geo\Console\ImportCommand;
use Leitom\Geo\Console\RemoveCommand;
use Illuminate\Support\ServiceProvider;

class GeoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('geo.php'),
            ], 'config');

            $this->commands([ImportCommand::class, RemoveCommand::class]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'geo');

        $this->app->singleton('geo', function ($app) {
            return new Geo($app);
        });
    }
}
