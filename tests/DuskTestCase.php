<?php

namespace S4mpp\Laraguard\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Dusk\{Options, TestCase};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

abstract class DuskTestCase extends TestCase
{
    use WithWorkbench;
    // use RefreshDatabase;

    protected static $baseServePort = 9000;

    protected function setUp(): void
    {
        parent::setUp();

        Options::withoutUI();

        RateLimiter::clear('rl:127.0.0.1');

        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }

    public static function panelProvider()
    {
        return [
            'Web' => [[
                'guard' => 'web',
                'uri' => 'restricted-area',
                'title' => 'Restricted area',
                'factory' => UserFactory::class,
                'can_register' => false,
                'redirect_to' => 'home',
            ]],
            'Customer' => [[
                'guard' => 'customer',
                'uri' => 'customer-area',
                'title' => 'Customer area',
                'factory' => CustomerFactory::class,
                'can_register' => true,
                'redirect_to' => 'my-account',
            ]],
        ];
    }

    protected function defineEnvironment($app): void
    {
        Config::set('database.default', 'dusk');
        Config::set('database.connections.dusk', [
            'driver' => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix' => '',
        ]);

        Config::push('view.paths', __DIR__.'/../workbench/resources/views');
    }
}
