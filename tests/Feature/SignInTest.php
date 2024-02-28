<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class SignInTest extends TestCase
{
    private $password = 'passwd918';

    public static function routeIndexProvider()
    {
        return [
            'web' => ['/restricted-area/signin', 200],
            'customer' => ['/customer-area/signin', 200],
            'non_existent_auth_page' => ['/customer-area/xxx', 404],
            'non_existent_panel' => ['/xxx/signin', 404],
        ];
    }

    /**
     * @dataProvider routeIndexProvider
     */
    public function test_route_index(string $route, int $expected_status_code): void
    {
        $response = $this->get($route);

        $response->assertStatus($expected_status_code);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_action($guard_name, $uri, $factory, $another_guard, $title, $redirect_to): void
    {
        $user = $factory::new()->create([
            'password' => Hash::make($this->password),
        ]);

        $response = $this->post('/'.$uri.'/signin', [
            'username' => $user->email,
            'password' => $this->password,
        ]);

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);
        $response->assertRedirectContains($uri.'/'.$redirect_to);

        $this->assertAuthenticatedAs($user, $guard_name);
        $this->assertNull(Auth::guard($another_guard)->user());
    }

    public function test_login_action_on_panel_with_invalid_model(): void
    {
        $response = $this->post('/guest-area/signin', [
            'username' => fake()->email(),
            'password' => '213456789',
        ]);

        $response->assertSessionHasErrorsIn('default');

        $response->assertStatus(302);
        $response->assertRedirectContains('/guest-area/signin');

        $this->assertNull(Auth::guard('guest')->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_action_route_invalid($guard_name, $uri, $factory, $another_guard): void
    {
        $user = $factory::new()->create([
            'password' => Hash::make($this->password),
        ]);

        $response = $this->post('/'.$guard_name.'/xxxxx', [
            'username' => $user->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(404);

        $this->assertNull(Auth::guard($guard_name)->user());
        $this->assertNull(Auth::guard($another_guard)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_action_other_guard($guard_name, $uri, $factory, $another_guard, $another_guard_url): void
    {
        $user = $factory::new()->create([
            'password' => Hash::make($this->password),
        ]);

        $response = $this->actingAs($user, $guard_name)->post('/'.$another_guard_url.'/signin', [
            'username' => $user->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/'.$another_guard_url.'/signin');
        $response->assertSessionHasErrorsIn('default');

        $this->assertNull(Auth::guard($another_guard)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_action_non_existent_data($guard_name, $uri, $factory, $another_guard): void
    {
        $this->get('/'.$uri.'/signin');
        $response = $this->post('/'.$uri.'/signin', [
            'email' => 'email@random.'.rand().'.com',
            'password' => 'rand123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/'.$uri.'/signin');
        $response->assertSessionHasErrorsIn('default');

        $this->assertNull(Auth::guard($guard_name)->user());
        $this->assertNull(Auth::guard($another_guard)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_action_invalid_password($guard_name, $uri, $factory, $another_guard): void
    {
        $user = $factory::new()->create();

        $this->get('/'.$uri.'/signin');
        $response = $this->post('/'.$uri.'/signin', [
            'username' => $user->email,
            'password' => 'rand123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/'.$uri.'/signin');
        $response->assertSessionHasErrorsIn('default');

        $this->assertNull(Auth::guard($guard_name)->user());
        $this->assertNull(Auth::guard($another_guard)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_action_blank_data($guard_name, $uri, $factory, $another_guard): void
    {
        $this->get('/'.$uri.'/signin');
        $response = $this->post('/'.$uri.'/signin', [
            'username' => null,
            'password' => null,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/'.$uri.'/signin');
        $response->assertSessionHasErrorsIn('default', ['username', 'password']);

        $this->assertNull(Auth::guard($guard_name)->user());
        $this->assertNull(Auth::guard($another_guard)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_access_restricted_area($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $guard_name)->get('/'.$uri.'/my-account');

        $response->assertStatus(200);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_access_restricted_area_not_logged($guard_name, $uri): void
    {
        $response = $this->get('/'.$uri.'/my-account');

        $response->assertStatus(302);
        $response->assertSessionHasErrorsIn('default');

        $response->assertRedirect('/'.$uri.'/signin');
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_access_restricted_area_other_guard_not_logged($guard_name, $uri, $factory, $another_guard, $another_guard_url): void
    {
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $guard_name)->get('/'.$another_guard_url.'/my-account');

        $response->assertStatus(302);
        $response->assertSessionHasErrorsIn('default');

        $response->assertRedirect('/'.$another_guard_url.'/signin');
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_logout($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $guard_name)->get('/'.$uri.'/signout');

        $response->assertStatus(302);

        $this->assertNull(Auth::guard($guard_name)->user());
    }
}
