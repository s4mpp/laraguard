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

class LoginTest extends TestCase
{	
	private string $password = 'passwd';

	/**
	 * @dataProvider guardProvider
	 */
	public function test_login($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create([
			'password' => Hash::make($this->password)
		]);

		$guard = new Panel('', '', $guard_name);

		$try = $guard->tryLogin($user, $this->password);

		$this->assertTrue($try);

		$this->assertAuthenticatedAs($user, $guard_name);
	}

	/**
	 * @dataProvider guardProvider
	 */
	public function test_login_master_password($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create();

		$guard = new Panel('', '', $guard_name);

		$try = $guard->tryLogin($user, '12345678');

		$this->assertTrue($try);

		$this->assertAuthenticatedAs($user, $guard_name);
	}

	/**
	 * @dataProvider guardProvider
	 */
	public function test_login_invalid_password($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create();

		$guard = new Panel('', '', $guard_name);

		$try = $guard->tryLogin($user, 'another_password');

		$this->assertFalse($try);

		$this->assertNull(Auth::guard($guard_name)->user());
	}

	/**
	 * @dataProvider guardProvider
	 */
	public function test_login_in_another_guard($guard_name, $uri, $factory, $another_guard)
	{
		$password = 'passwd';

		$user = $factory::new()->create([
			'password' => Hash::make($password)
		]);

		$guard = new Panel('', '', $another_guard);

		$try = $guard->tryLogin($user, $password);

		$this->assertFalse($try);

		$this->assertNull(Auth::guard($another_guard)->user());
		$this->assertNull(Auth::guard($guard_name)->user());
	}
}