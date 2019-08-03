<?php

namespace Leitom\Geo\Tests;

use Leitom\Geo\GeoServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database');
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->configureDatabase($app)
             ->configureRedis($app);
    }

    protected function configureDatabase($app)
    {
        $app['config']->set('database.default', 'geo');

        $app['config']->set('database.connections.geo', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        return $this;
    }

    protected function configureRedis($app)
    {
        $app['config']->set('database.redis', [
            'client' => 'predis',
            'default' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
            ],
        ]);

        return $this;
    }

    protected function getPackageProviders($app)
    {
        return [GeoServiceProvider::class];
    }
}
