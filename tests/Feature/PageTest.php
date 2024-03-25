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
            'home' => ['area-restrita/home', 200],
            'page' => ['area-restrita/module/page', 200],
            'page 2' => ['area-restrita/module-2/page', 200],
            'subsection 1' => ['area-restrita/section/subsection-1', 200],
            'subsection 2' => ['area-restrita/section/subsection-2', 200],
            'invoke layout default' => ['area-restrita/invoke-layout-default', 200],
            'invoke layout' => ['area-restrita/invoke-layout', 200],
            'non existent page' => ['/area-restrita/xxx', 404],
            'no index' => ['/area-restrita/no-index', 404],
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
