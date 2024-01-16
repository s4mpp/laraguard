<?php

namespace S4mpp\Laraguard\Tests;

use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase as BaseTestCase;
use S4mpp\Laraguard\Providers\LaraguardServiceProvider;
use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\workbench_path;


abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
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
     * Get Application base path.
     *
     * @return string
     */
    // public static function applicationBasePath()
    // {
    //     var_dump(__DIR__);
    //     // return __DIR__.'/../skeleton';
    // }

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
        
    }
}
