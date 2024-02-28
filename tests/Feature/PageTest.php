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
            'home' => ['restricted-area/home', 200],
            'page' => ['restricted-area/module/page', 200],
            'page 2' => ['restricted-area/module-2/page', 200],
            'subsection 1' => ['restricted-area/section/subsection-1', 200],
            'subsection 2' => ['restricted-area/section/subsection-2', 200],
            'invoke layout default' => ['restricted-area/invoke-layout-default', 200],
            'invoke layout' => ['restricted-area/invoke-layout', 200],
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
