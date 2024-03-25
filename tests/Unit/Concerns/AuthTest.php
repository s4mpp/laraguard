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
     * @dataProvider panelProvider
     */
    public function test_login($panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create([
            'password' => Hash::make($this->password),
        ]);

        $p = new Panel('', '', $panel['guard_name']);

        $try = ConcernsAuth::tryLogin($p, $user, $this->password);

        $this->assertTrue($try);

        $this->assertAuthenticatedAs($user, $panel['guard_name']);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_master_password($panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $p = new Panel('', '', $panel['guard_name']);

        $try = ConcernsAuth::tryLogin($p, $user, '12345678');

        $this->assertTrue($try);

        $this->assertAuthenticatedAs($user, $panel['guard_name']);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_invalid_password($panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $p = new Panel('', '', $panel['guard_name']);

        $try = ConcernsAuth::tryLogin($p, $user, 'another_password');

        $this->assertFalse($try);

        $this->assertNull(Auth::guard($panel['guard_name'])->user());
    }

    
    public function test_login_in_another_guard(): void
    {
        $password = 'passwd';

        $user = UserFactory::new()->create([
            'password' => Hash::make($password),
        ]);

        $p = new Panel('', '', 'customer');

        $try = ConcernsAuth::tryLogin($p, $user, $password);

        $this->assertFalse($try);

        $this->assertNull(Auth::guard('customer')->user());
        $this->assertNull(Auth::guard('web')->user());
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_logout($panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $p = new Panel('', '', $panel['guard_name']);

        Auth::guard($panel['guard_name'])->login($user);

        ConcernsAuth::logout($p->getGuardName());

        $this->assertNull(Auth::guard($panel['guard_name'])->user());
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_logout_when_not_logged($panel): void
    {
        $p = new Panel('', '', $panel['guard_name']);

        $logout = ConcernsAuth::logout($p->getGuardName());

        $this->assertTrue($logout);

        $this->assertNull(Auth::guard($panel['guard_name'])->user());
    }
}
