<?php

namespace S4mpp\Laraguard\Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Workbench\Database\Factories\UserFactory;
use Workbench\Database\Factories\CustomerFactory;

class ChangePasswordTest extends TestCase
{
	public static function invalidDataProvider()
	{
		return [
			'E-mail nulo' => ['email', null],
			'E-mail empty' => ['email', ''],
			'E-mail invalid' => ['email', '1111'],
			'Token nulo' => ['token', null],
			'Token empty' => ['token', ''],
			'Password nulo' => ['password', null],
			'Password empty' => ['password', ''],
			'Password small' => ['password', '123'],
			'Password confirmation nulo' => ['password_confirmation', null],
			'Password confirmation empty' => ['password_confirmation', ''],
			'Password confirmation small' => ['password_confirmation', '123'],
		];
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_index_page_with_invalid_code($guard_name, $uri, $factory)
	{	
		$response = $this->get('/'.$uri.'/password-recovery/change/xxxxxxxxxxx');
	
		$response->assertSessionHasErrors();
		
		$response->assertStatus(302);
		$response->assertRedirect('/'.$uri.'/password-recovery');
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_try_reset_password_with_invalid_code($guard_name, $uri, $factory)
	{	
		$response = $this->put('/'.$uri.'/password-recovery/change', [
			'email' => 'teste@email.com',
			'token' => 'xxxxxxxxx',
			'password' => '12345678',
			'password_confirmation' => '12345678',
		]);
	
		$response->assertSessionHasErrors();
		
		$response->assertStatus(302);
		$response->assertRedirect('/'.$uri.'/password-recovery');
	}

	/**
	 *
	 * @dataProvider invalidDataProvider
	 */
	public function test_try_reset_password_with_invalid_data($field, $value = null)
	{
		$data = [
			'email' => 'teste@email.com',
			'token' => 'xxxxxxxxx',
			'password' => '12345678',
			'password_confirmation' => '12345678',
		];

		$data[$field] = $value;

		$this->get('/restricted-area/password-recovery/change/xxxxxxxxx');
		$response = $this->put('/restricted-area/password-recovery/change', $data);
	
		$response->assertSessionHasErrors($field);
		
		$response->assertStatus(302);
		$response->assertRedirect('/restricted-area/password-recovery/change/xxxxxxxxx');
	}


	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_try_reset_password_with_password_unconfirmed()
	{
		$data = [
			'email' => 'teste@email.com',
			'token' => 'xxxxxxxxx',
			'password' => '789456123',
			'password_confirmation' => '12345678',
		];

		$this->get('/restricted-area/password-recovery/change/xxxxxxxxx');
		$response = $this->put('/restricted-area/password-recovery/change', $data);
	
		$response->assertSessionHasErrors('password');
		
		$response->assertStatus(302);
		$response->assertRedirect('/restricted-area/password-recovery/change/xxxxxxxxx');
	}
}