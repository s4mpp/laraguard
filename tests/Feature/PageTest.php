<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class PageTest extends TestCase
{
    public static function routeIndexProvider()
    {
        return [
            'my account' => ['/restricted-area/my-account', 200],
            'module controller' => ['/restricted-area/section-1/orders', 200],
            'non existent page' => ['/restricted-area/xxx', 404],
            'no index' => ['/restricted-area/no-index', 404],
            'non existent panel' => ['/another-thing', 404],
        ];
    }

    /**
     * @dataProvider routeIndexProvider
     */
    public function test_route_page(string $route, int $expected_status_code): void
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get($route);

        $response->assertStatus($expected_status_code);
    }
}
