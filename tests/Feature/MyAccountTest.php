<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Workbench\Database\Factories\UserFactory;

class MyAccountTest extends TestCase
{
	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_Index_page($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create();

		$response = $this->actingAs($user, $guard_name)->get($uri.'/my-account');

		$response->assertStatus(200);
		$response->assertSee($user->name);
		$response->assertSee($user->email);
	}
}