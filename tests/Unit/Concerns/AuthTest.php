<?php

namespace S4mpp\Laraguard\Tests\Unit\Concerns;

use RuntimeException;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use S4mpp\Laraguard\Concerns\Auth as ConcernsAuth;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class AuthTest extends TestCase
{
    private string $password = 'passwd';

    /**
     * @dataProvider guardProvider
     */
    public function test_login($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create([
            'password' => Hash::make($this->password),
        ]);

        $panel = new Panel('', '', $guard_name);

        $try = ConcernsAuth::tryLogin($panel, $user, $this->password);

        $this->assertTrue($try);

        $this->assertAuthenticatedAs($user, $guard_name);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_master_password($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $panel = new Panel('', '', $guard_name);

        $try = ConcernsAuth::tryLogin($panel, $user, '12345678');

        $this->assertTrue($try);

        $this->assertAuthenticatedAs($user, $guard_name);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_invalid_password($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $panel = new Panel('', '', $guard_name);

        $try = ConcernsAuth::tryLogin($panel, $user, 'another_password');

        $this->assertFalse($try);

        $this->assertNull(Auth::guard($guard_name)->user());
    }

    

    /**
     * @dataProvider guardProvider
     */
    public function test_login_in_another_guard($guard_name, $uri, $factory, $another_guard): void
    {
        $password = 'passwd';

        $user = $factory::new()->create([
            'password' => Hash::make($password),
        ]);

        $panel = new Panel('', '', $another_guard);

        $try = ConcernsAuth::tryLogin($panel, $user, $password);

        $this->assertFalse($try);

        $this->assertNull(Auth::guard($another_guard)->user());
        $this->assertNull(Auth::guard($guard_name)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_logout($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $panel = new Panel('', '', $guard_name);

        Auth::guard($guard_name)->login($user);

        ConcernsAuth::logout($panel);

        $this->assertNull(Auth::guard($guard_name)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_logout_when_not_logged($guard_name, $uri, $factory): void
    {
        $panel = new Panel('', '', $guard_name);

        $logout = ConcernsAuth::logout($panel);

        $this->assertTrue($logout);

        $this->assertNull(Auth::guard($guard_name)->user());
    }
}
