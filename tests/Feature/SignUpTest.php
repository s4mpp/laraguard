<?php

namespace S4mpp\Laraguard\Tests\Feature;

use Exception;
use S4mpp\Laraguard\Tests\TestCase;

final class SignUpTest extends TestCase
{
    public static function routeIndexProvider()
    {
        return [
            'web' => ['/restricted-area/signup', 404],
            'web user registered' => ['/restricted-area/signup/user-registered', 404],
            'customer' => ['/customer-area/signup', 200],
            'customer user registered' => ['/customer-area/signup/user-registered', 200],
        ];
    }

    /**
     * @dataProvider routeIndexProvider
     */
    public function test_route_index(string $route, int $expected_status_code): void
    {
        $response = $this->get($route);

        $response->assertStatus($expected_status_code);
    }

    public function test_action_register(): void
    {
        $response = $this->post('/customer-area/signup', [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/customer-area/signup/user-registered');
    }

    public function test_action_register_on_panel_with_model_invalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid model');

        $response = $this->withoutExceptionHandling()->post('/guest-area/signup', [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);
    }

    public function test_action_register_on_panel_not_allowed(): void
    {
        $response = $this->post('/restricted-area/signup', [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $response->assertStatus(404);
    }
}
