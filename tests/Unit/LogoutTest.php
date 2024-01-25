<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Illuminate\Foundation\Auth\User;
use RuntimeException;
use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\CustomerFactory;
use Workbench\Database\Factories\UserFactory;

class LogoutTest extends TestCase
{	
	/**
	 * @dataProvider guardProvider
	 */
	public function test_logout($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create();

		$guard = new Guard('', '', $guard_name);
		
		Auth::guard($guard_name)->login($user);

		$guard->logout();

		$this->assertNull(Auth::guard($guard_name)->user());
	}

	/**
	 * @dataProvider guardProvider
	 */
	public function test_logout_when_not_logged($guard_name, $uri, $factory)
	{
		$guard = new Guard('', '', $guard_name);
		
		$guard->logout();

		$this->assertNull(Auth::guard($guard_name)->user());
	}
}