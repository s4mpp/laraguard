<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\UserFactory;
use Workbench\Database\Factories\CustomerFactory;

class PageTest extends TestCase
{
	public static function routeIndexProvider()
	{
		return [
			'my-account' => ['/restricted-area/my-account', 200],
			'non_existent_page' => ['/restricted-area/xxx', 404],
		];
	}

	/**
	 *
	 * @dataProvider routeIndexProvider
	 */
	public function test_route_page(string $route, int $expected_status_code)
	{
		$user = UserFactory::new()->create();

		$response = $this->actingAs($user)->get($route);

		$response->assertStatus($expected_status_code);
	}
}