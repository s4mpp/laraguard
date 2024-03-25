<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class StartTest extends TestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_route_start_logged($panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $panel['guard_name'])->get($panel['prefix']);

        $response->assertStatus(302);
        $response->assertRedirect($panel['prefix'].'/'.$panel['redirect_to_after_login']);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_route_start_not_logged($panel): void
    {
        $response = $this->get($panel['prefix']);

        $response->assertStatus(302);
        $response->assertRedirect($panel['prefix'].'/entrar');
    }

    
    public function test_route_start_logged_in_another_guard(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user, 'web')->get('area-do-cliente');

        $response->assertStatus(302);
        $response->assertRedirect('area-do-cliente/entrar');
    }
}
