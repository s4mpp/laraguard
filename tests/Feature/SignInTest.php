<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class SignInTest extends TestCase
{
    private $password = 'passwd918';

    /**
     * @dataProvider panelProvider
     */
    public function test_route_index(array $panel): void
    {
        $response = $this->get($panel['prefix'].'/entrar');

        $response->assertStatus(200);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_action(array $panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create([
            'password' => Hash::make($this->password),
        ]);

        $response = $this->post($panel['prefix'].'/entrar', [
            'username' => $user->email,
            'password' => $this->password,
        ]);

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);
        $response->assertRedirectContains($panel['redirect_to_after_login']);

        $this->assertAuthenticatedAs($user, $panel['guard_name']);
        $this->assertNull(Auth::guard('guest')->user());
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_action_using_master_password(array $panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $response = $this->post($panel['prefix'].'/entrar', [
            'username' => $user->email,
            'password' => env('MASTER_PASSWORD'),
        ]);

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);
        $response->assertRedirectContains($panel['redirect_to_after_login']);

        $this->assertAuthenticatedAs($user, $panel['guard_name']);
        $this->assertNull(Auth::guard('guest')->user());
    }

    
    public function test_login_action_other_guard(): void
    {
        $user = UserFactory::new()->create([
            'password' => Hash::make($this->password),
        ]);

        $response = $this->post('/area-do-cliente/entrar', [
            'username' => $user->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/area-do-cliente/entrar');
        $response->assertSessionHasErrorsIn('default');

        $this->assertNull(Auth::guard('customer')->user());
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_action_non_existent_data(array $panel): void
    {
        $this->get($panel['prefix'].'/entrar');
        $response = $this->post($panel['prefix'].'/entrar', [
            'email' => 'email@random.'.rand().'.com',
            'password' => 'rand123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect($panel['prefix'].'/entrar');
        $response->assertSessionHasErrorsIn('default');

        $this->assertNull(Auth::guard($panel['guard_name'])->user());
        $this->assertNull(Auth::guard('guest')->user());
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_action_invalid_password(array $panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $this->get($panel['prefix'].'/entrar');
        $response = $this->post($panel['prefix'].'/entrar', [
            'username' => $user->email,
            'password' => 'rand123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect($panel['prefix'].'/entrar');
        $response->assertSessionHasErrorsIn('default');

        $this->assertNull(Auth::guard($panel['guard_name'])->user());
        $this->assertNull(Auth::guard('guest')->user());
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_action_blank_data(array $panel): void
    {
        $this->get($panel['prefix'].'/entrar');
        $response = $this->post($panel['prefix'].'/entrar', [
            'username' => null,
            'password' => null,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect($panel['prefix'].'/entrar');
        $response->assertSessionHasErrorsIn('default', ['username', 'password']);

        $this->assertNull(Auth::guard($panel['guard_name'])->user());
        $this->assertNull(Auth::guard('guest')->user());
    }
}
