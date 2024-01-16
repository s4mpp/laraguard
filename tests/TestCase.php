<?php

namespace S4mpp\Laraguard\Tests;

use Workbench\App\Models\Customer;
use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\workbench_path;
use Orchestra\Testbench\TestCase as BaseTestCase;
use S4mpp\Laraguard\Providers\LaraguardServiceProvider;


abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        artisan($this, 'migrate', ['--database' => 'testbench']);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }

    /**
     * add the package provider
     *
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaraguardServiceProvider::class];
    }


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'customers'
        ]);

        $app['config']->set('auth.providers.customers', [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ]);
        
    }
}
