<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class RestrictedAreaTest extends TestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_access_restricted_area(array $panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $panel['guard_name'])->get($panel['prefix'].'/minha-conta');

        $response->assertStatus(200);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_access_restricted_area_not_logged(array $panel): void
    {
        $response = $this->get($panel['prefix'].'/'.$panel['redirect_to_after_login']);

        $response->assertStatus(302);

        $response->assertRedirect($panel['prefix'].'/entrar');
    }

    
    public function test_access_restricted_area_other_guard_not_logged(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user, 'web')->get('/area-do-cliente/minha-conta');

        $response->assertStatus(302);

        $response->assertRedirect('/area-do-cliente/entrar');
    }
}
