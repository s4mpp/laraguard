<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;

final class MyAccountTest extends TestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_index_page($panel): void
    {
        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $panel['guard_name'])->get($panel['prefix'].'/minha-conta');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }
}
