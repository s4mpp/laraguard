<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class LogoutTest extends TestCase
{

    /**
     * @dataProvider panelProvider
     */
    public function test_logout(array $panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $panel['guard_name'])->get($panel['prefix'].'/sair');

        $response->assertStatus(302);

        $this->assertNull(Auth::guard($panel['guard_name'])->user());
    }
}
