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

class TryLoginTest extends TestCase
{
	private User $user;
	
	private string $password = 'passwd';

	private Guard $guard;

	protected function setUp(): void
	{
		parent::setUp();

		$this->user = CustomerFactory::new()->create([
			'password' => Hash::make($this->password)
		]);

		$this->guard = new Guard('', '', 'customer');
	}

	public function test_login()
	{
		$try = $this->guard->tryLogin($this->user, $this->password);

		$this->assertTrue($try);

		$this->assertAuthenticatedAs($this->user, 'customer');
	}

	public function test_login_master_password()
	{
		$try = $this->guard->tryLogin($this->user, '12345678');

		$this->assertTrue($try);

		$this->assertAuthenticatedAs($this->user, 'customer');
	}

	public function test_login_invalid_password()
	{
		$try = $this->guard->tryLogin($this->user, 'another_password');

		$this->assertFalse($try);

		$this->assertNull(Auth::guard('customer')->user());
	}

	public function test_redirect_to_inside()
	{
		Auth::guard('customer')->login($this->user);

		$redirect = $this->guard->redirectToInside();

		$this->assertIsString($redirect);
	}

	public function test_redirect_to_inside_not_authenticated()
	{
		$redirect = $this->guard->redirectToInside();

		$this->assertEquals($redirect, 'User is logged in');
	}

	public function test_logout()
	{
		Auth::guard('customer')->login($this->user);

		$this->guard->logout();

		$this->assertNull(Auth::guard('customer')->user());
	}
}