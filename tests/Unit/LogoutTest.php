<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class LogoutTest extends TestCase
{
    /**
     * @dataProvider guardProvider
     */
    public function test_logout($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $guard = new Panel('', '', $guard_name);

        Auth::guard($guard_name)->login($user);

        $guard->auth()->logout();

        $this->assertNull(Auth::guard($guard_name)->user());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_logout_when_not_logged($guard_name, $uri, $factory): void
    {
        $guard = new Panel('', '', $guard_name);

        $guard->auth()->logout();

        $this->assertNull(Auth::guard($guard_name)->user());
    }
}
