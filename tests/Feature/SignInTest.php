<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Laraguard;
use Workbench\Database\Factories\UserFactory;
use Workbench\Database\Factories\CustomerFactory;

class SignInTest extends TestCase
{
	private $password = 'passwd918';

	public static function routeIndexProvider()
	{
		return [
			'web' => ['/restricted-area/signin', 200],
			'customer' => ['/customer-area/signin', 200],
			'non_existent_auth_page' => ['/customer-area/xxx', 404],
			'non_existent_panel' => ['/xxx/signin', 404]
		];
	}

	/**
	 *
	 * @dataProvider routeIndexProvider
	 */
	public function test_route_index(string $route, int $expected_status_code)
	{
		$response = $this->get($route);

		$response->assertStatus($expected_status_code);
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_login_action($guard_name, $uri, $factory, $another_guard)
	{
		$user = $factory::new()->create([
			'password' => Hash::make($this->password)
		]);

		$response = $this->post('/'.$uri.'/signin', [
			'email' => $user->email,
			'password' => $this->password
		]);

		$response->assertSessionHasNoErrors();

		$response->assertStatus(302);
		$response->assertRedirectContains('my-account/index');

		$this->assertAuthenticatedAs($user, $guard_name);
		$this->assertNull(Auth::guard($another_guard)->user());
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_login_action_route_invalid($guard_name, $uri, $factory, $another_guard)
	{
		$user = $factory::new()->create([
			'password' => Hash::make($this->password)
		]);

		$response = $this->post('/'.$guard_name.'/xxxxx', [
			'email' => $user->email,
			'password' => $this->password
		]);

		$response->assertStatus(404);		

		$this->assertNull(Auth::guard($guard_name)->user());
		$this->assertNull(Auth::guard($another_guard)->user());
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_login_action_other_guard($guard_name, $uri, $factory, $another_guard, $another_guard_url)
	{
		$user = $factory::new()->create([
			'password' => Hash::make($this->password)
		]);

		$response = $this->actingAs($user, $guard_name)->post('/'.$another_guard_url.'/signin', [
			'email' => $user->email,
			'password' => $this->password
		]);

		$response->assertStatus(302);		
		$response->assertRedirect('/'.$another_guard_url.'/signin');
		$response->assertSessionHasErrorsIn('default');

		$this->assertNull(Auth::guard($another_guard)->user());
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_login_action_non_existent_data($guard_name, $uri, $factory, $another_guard)
	{
		$this->get('/'.$uri.'/signin');
		$response = $this->post('/'.$uri.'/signin', [
			'email' => 'email@random.'.rand().'.com',
			'password' => 'rand123'
		]);

		$response->assertStatus(302);		
		$response->assertRedirect('/'.$uri.'/signin');
		$response->assertSessionHasErrorsIn('default');

		$this->assertNull(Auth::guard($guard_name)->user());
		$this->assertNull(Auth::guard($another_guard)->user());
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_login_action_invalid_password($guard_name, $uri, $factory, $another_guard)
	{
		$user = $factory::new()->create();

		$this->get('/'.$uri.'/signin');
		$response = $this->post('/'.$uri.'/signin', [
			'email' => $user->email,
			'password' => 'rand123'
		]);

		$response->assertStatus(302);		
		$response->assertRedirect('/'.$uri.'/signin');
		$response->assertSessionHasErrorsIn('default');

		$this->assertNull(Auth::guard($guard_name)->user());
		$this->assertNull(Auth::guard($another_guard)->user());
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_login_action_blank_data($guard_name, $uri, $factory, $another_guard)
	{
		$this->get('/'.$uri.'/signin');
		$response = $this->post('/'.$uri.'/signin', [
			'email' => null,
			'password' => null
		]);

		$response->assertStatus(302);		
		$response->assertRedirect('/'.$uri.'/signin');
		$response->assertSessionHasErrorsIn('default', ['email', 'password']);

		$this->assertNull(Auth::guard($guard_name)->user());
		$this->assertNull(Auth::guard($another_guard)->user());
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_access_restricted_area($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create();

		$response = $this->actingAs($user, $guard_name)->get('/'.$uri.'/my-account/index');

		$response->assertStatus(200);
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_access_restricted_area_not_logged($guard_name, $uri)
	{
		$response = $this->get('/'.$uri.'/my-account/index');

		$response->assertStatus(302);
		$response->assertSessionHasErrorsIn('default');
		
		$response->assertRedirect('/'.$uri.'/signin');
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_access_restricted_area_other_guard_not_logged($guard_name, $uri, $factory, $another_guard, $another_guard_url)
	{
		$user = $factory::new()->create();

		$response = $this->actingAs($user, $guard_name)->get('/'.$another_guard_url.'/my-account/index');

		$response->assertStatus(302);
		$response->assertSessionHasErrorsIn('default');
		
		$response->assertRedirect('/'.$another_guard_url.'/signin');
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_logout($guard_name, $uri, $factory)
	{
		$user = $factory::new()->create();

		$response = $this->actingAs($user, $guard_name)->get('/'.$uri.'/signout');

		$response->assertStatus(302);

		$this->assertNull(Auth::guard($guard_name)->user());
	}
}