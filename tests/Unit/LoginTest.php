<?php

namespace S4mpp\Laraguard\Tests\Unit;

use App\Models\User;
use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Tests\TestCase;

class LoginTest extends TestCase
{
	public function test_login()
	{

		// $this->assertTrue(true);

		$new_guard = new Guard('Panel title', 'panel-prefix', 'web');

		$new_guard->tryLogin('user', 'password');
	}
	
	public function test_login_invalid_username()
	{
		
	}

	public function test_login_invalid_password()
	{
		
	}

	public function test_redirect_to_inside()
	{
		
	}
}