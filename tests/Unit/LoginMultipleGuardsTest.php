<?php

namespace S4mpp\Laraguard\Tests\Unit;

use RuntimeException;
use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\CustomerFactory;
use Workbench\Database\Factories\UserFactory;

class LoginMultipleGuardsTest extends TestCase
{
	public static function guardProvider()
	{
		return [
			'Web' => ['web', UserFactory::class, 'customer'],
			'Customer' => ['customer', CustomerFactory::class, 'web'],
		];
	}

	/**
	 * @dataProvider guardProvider
	 */
	public function test_login($guard_name, $factory)
	{
		$password = 'passwd';

		$user = $factory::new()->create([
			'password' => Hash::make($password)
		]);

		$guard = new Guard('', '', $guard_name);

		$try = $guard->tryLogin($user, $password);

		$this->assertTrue($try);

		$this->assertAuthenticatedAs($user, $guard_name);
	}

	/**
	 * @dataProvider guardProvider
	 */
	public function test_login_in_another_guard($guard_name, $factory, $another_guard)
	{
		$password = 'passwd';

		$user = $factory::new()->create([
			'password' => Hash::make($password)
		]);

		$guard = new Guard('', '', $another_guard);

		$try = $guard->tryLogin($user, $password);

		$this->assertFalse($try);

		$this->assertNull(Auth::guard($another_guard)->user());
		$this->assertNull(Auth::guard($guard_name)->user());
	}
}