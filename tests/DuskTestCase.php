<?php

namespace S4mpp\Laraguard\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use S4mpp\Laraguard\Tests\InteractsWithPanels;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Dusk\{Options, TestCase};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

abstract class DuskTestCase extends TestCase
{
    use WithWorkbench, InteractsWithPanels;

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
