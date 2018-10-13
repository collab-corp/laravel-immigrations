<?php

namespace CollabCorp\LaravelImmigrations\Tests;

use CollabCorp\LaravelImmigrations\LaravelImmigrationsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelImmigrationsServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup the "old_database", the default from database.
        $app['config']->set('database.connections.old_database', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
