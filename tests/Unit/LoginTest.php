<?php

namespace S4mpp\Laraguard\Tests\Unit;

use RuntimeException;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class LoginTest extends TestCase
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

        $guard = new Panel('', '', $guard_name);

        $try = $guard->auth()->tryLogin($user, $this->password);

        $this->assertTrue($try);

        $this->assertAuthenticatedAs($user, $guard_name);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_master_password($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $guard = new Panel('', '', $guard_name);

        $try = $guard->auth()->tryLogin($user, '12345678');

        $this->assertTrue($try);

        $this->assertAuthenticatedAs($user, $guard_name);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_login_invalid_password($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $guard = new Panel('', '', $guard_name);

        $try = $guard->auth()->tryLogin($user, 'another_password');

        $this->assertFalse($try);

        $this->assertNull(Auth::guard($guard_name)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_check_password($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create(['password' => Hash::make('p455w9rd')]);

        $panel = new Panel('', '', $guard_name);

        $test = $panel->auth()->check($user, 'p455w9rd');

        $this->assertTrue($test);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_check_wrong_password($guard_name, $uri, $factory): void
    {
        $this->expectException(RuntimeException::class);

        $user = $factory::new()->create(['password' => Hash::make('p455w9rd')]);

        $panel = new Panel('', '', $guard_name);

        $test = $panel->auth()->check($user, 'xxxxxx123');

        $this->assertNull($test);
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

        $guard = new Panel('', '', $another_guard);

        $try = $guard->auth()->tryLogin($user, $password);

        $this->assertFalse($try);

        $this->assertNull(Auth::guard($another_guard)->user());
        $this->assertNull(Auth::guard($guard_name)->user());
    }
}
