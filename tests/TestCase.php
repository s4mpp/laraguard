<?php

namespace S4mpp\Laraguard\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Config\Repository;
use Workbench\Database\Factories\UserFactory;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Workbench\Database\Factories\CustomerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench, InteractsWithViews;
	use RefreshDatabase;

    public static function guardProvider()
	{
		return [
			'Web' => ['web', 'restricted-area', UserFactory::class, 'customer', 'customer-area'],
			'Customer' => ['customer',  'customer-area', CustomerFactory::class, 'web', 'restricted-area'],
		];
	}

	protected function defineEnvironment($app)
    {
        Config::set('database.default', 'testing');
    }
}
