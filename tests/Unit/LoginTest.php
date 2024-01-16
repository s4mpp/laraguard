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

class LoginTest extends TestCase
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
		$try = $this->guard->tryLogin($this->user->email, $this->password);

		$this->assertTrue($try);

		$this->assertAuthenticatedAs($this->user, 'customer');
	}
	
	public function test_login_invalid_username()
	{
		$this->expectException(RuntimeException::class);
	
		$try = $this->guard->tryLogin('anotheremail@email.com', $this->password);

		$this->assertEquals(Auth::guard('customer')->user(), null);

		$this->assertIsNull($try);
	}

	public function test_login_invalid_password()
	{
		$try = $this->guard->tryLogin($this->user->email, 'another_password');

		$this->assertFalse($try);

		$this->assertEquals(Auth::guard('customer')->user(), null);
	}

	public function test_redirect_to_inside()
	{
		Auth::guard('customer')->login($this->user);

		$redirect = $this->guard->redirectToInside();

		$this->assertIsString($redirect);
	}

	public function test_redirect_to_inside_not_authenticated()
	{
		$this->expectException(RuntimeException::class);

		$redirect = $this->guard->redirectToInside();

		$this->assertIsNull($redirect);
	}
}