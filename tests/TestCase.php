<?php

namespace S4mpp\Laraguard\Tests;

use Workbench\Database\Factories\UserFactory;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Workbench\Database\Factories\CustomerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench, RefreshDatabase;

    public static function guardProvider()
	{
		return [
			'Web' => ['web', 'restricted-area', UserFactory::class, 'customer', 'customer-area'],
			'Customer' => ['customer',  'customer-area', CustomerFactory::class, 'web', 'restricted-area'],
		];
	}
}
