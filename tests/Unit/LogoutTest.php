<?php

namespace S4mpp\Laraguard\Tests\Unit;

use RuntimeException;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\UserFactory;
use Workbench\Database\Factories\CustomerFactory;

class LogoutTest extends TestCase
{	
	/**
	 * @dataProvider guardProvider
	 */
	public function test_logout($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create();

		$guard = new Panel('', '', $guard_name);
		
		Auth::guard($guard_name)->login($user);

		$guard->logout();

		$this->assertNull(Auth::guard($guard_name)->user());
	}

	/**
	 * @dataProvider guardProvider
	 */
	public function test_logout_when_not_logged($guard_name, $uri, $factory)
	{
		$guard = new Panel('', '', $guard_name);
		
		$guard->logout();

		$this->assertNull(Auth::guard($guard_name)->user());
	}
}