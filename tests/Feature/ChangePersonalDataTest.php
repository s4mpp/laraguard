<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ChangePersonalDataTest extends TestCase
{
	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_change_personal_data($guard_name, $uri, $factory)
	{
		$current_password = 'passw0rd';
		
		$old_data = ['name' => fake()->name(), 'email' => fake()->safeEmail(),];
		$new_data = ['name' => fake()->name(), 'email' => fake()->safeEmail(),];

		$user = $factory::new([
			'name' => $old_data['name'],
			'email' => $old_data['email'],
			'password' => Hash::make($current_password)
		])->create();


		$this->get('/'.$uri.'/my-account');
		$response = $this->actingAs($user, $guard_name)->put('/'.$uri.'/my-account/save-personal-data', [
			'current_password' => $current_password,
			'name' => $new_data['name'],
			'email' => $new_data['email'],
		]);

		$response->assertStatus(302);
		$response->assertSessionHasNoErrors();
		$response->assertRedirect('/'.$uri.'/my-account');

		$table = app($factory::new()->modelName())->getTable();

		$this->assertDatabaseHas($table, [
			'name' => $new_data['name'],
			'email' => $new_data['email'],
		]);

		$this->assertDatabaseMissing($table, [
			'name' => $old_data['name'],
			'email' => $old_data['email'],
		]);
	}
}