<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class StartTest extends TestCase
{
    /**
     * @dataProvider guardProvider
     */
    public function test_route_start_logged($guard_name, $uri, $factory): void
    {
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $guard_name)->get($uri);

        $response->assertStatus(302);
        $response->assertRedirect($uri.'/my-account');
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_route_start_not_logged($guard_name, $uri): void
    {
        $response = $this->get($uri);

        $response->assertStatus(302);
        $response->assertRedirect($uri.'/signin');
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_route_start_logged_in_another_guard($guard_name, $uri, $factory, $another_guard, $another_uri): void
    {
        $user = $factory::new()->create();

        $response = $this->actingAs($user, $guard_name)->get($another_uri);

        $response->assertStatus(302);
        $response->assertRedirect($another_uri.'/signin');
    }
}
