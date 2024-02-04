<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ChangePasswordTest extends TestCase
{
	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_change_password($guard_name, $uri, $factory)
	{
		$current_password = 'pa55word';
		$new_password = 'p4sword';
		
		$user = $factory::new([
			'password' => Hash::make($current_password)
		])->create();

		$this->get('/'.$uri.'/my-account');
		$response = $this->actingAs($user, $guard_name)->put('/'.$uri.'/my-account/change-password', [
			'current_password' => $current_password,
			'password' => $new_password,
			'password_confirmation' => $new_password,
		]);

		$response->assertStatus(302);
		$response->assertSessionHasNoErrors();
		$response->assertRedirect('/'.$uri.'/my-account');

		$user->refresh();

		
	}
}