<?php

namespace S4mpp\Laraguard\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use S4mpp\Laraguard\Tests\InteractsWithPanels;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithWorkbench, InteractsWithPanels;

    public function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('rl:127.0.0.1');
    }

    protected function defineEnvironment($app): void
    {
        Config::set('database.default', 'testing');
    }
}
