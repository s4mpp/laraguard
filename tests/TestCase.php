<?php

namespace S4mpp\Laraguard\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithWorkbench;

    public function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('rl:127.0.0.1');
    }

    public static function guardProvider()
    {
        return [
            'Web' => ['web', 'restricted-area', UserFactory::class, 'customer', 'customer-area', 'home'],
            'Customer' => ['customer',  'customer-area', CustomerFactory::class, 'web', 'restricted-area', 'my-account'],
        ];
    }

    protected function defineEnvironment($app): void
    {
        Config::set('database.default', 'testing');
    }
}
