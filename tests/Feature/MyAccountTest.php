<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;

final class MyAccountTest extends TestCase
{
    /**
     * @dataProvider guardProvider
     */
    public function test_index_page($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $guard_name)->get($uri.'/my-account');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }
}
