<?php

namespace S4mpp\Laraguard\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

abstract class TestCase extends BaseTestCase
{
    use InteractsWithViews;
    use RefreshDatabase;
    use WithWorkbench;

    public static function guardProvider()
    {
        return [
            'Web' => ['web', 'restricted-area', UserFactory::class, 'customer', 'customer-area'],
            'Customer' => ['customer',  'customer-area', CustomerFactory::class, 'web', 'restricted-area'],
        ];
    }

    protected function defineEnvironment($app): void
    {
        Config::set('database.default', 'testing');
    }
}
