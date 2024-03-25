<?php

namespace S4mpp\Laraguard\Tests\Feature;

use Exception;
use S4mpp\Laraguard\Tests\TestCase;

final class SignUpTest extends TestCase
{
    public static function routeIndexProvider()
    {
        return [
            'web' => ['/area-restrita/cadastro', 404],
            'web user registered' => ['/area-restrita/cadastro/finalizado', 404],
            'customer' => ['/area-do-cliente/cadastro', 200],
            'customer user registered' => ['/area-do-cliente/cadastro/finalizado', 200],
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
        $response = $this->post('/area-do-cliente/cadastro', [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/area-do-cliente/cadastro/finalizado');
    }

    public function test_action_register_on_panel_not_allowed(): void
    {
        $response = $this->post('/area-restrita/signup', [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $response->assertStatus(404);
    }
}
