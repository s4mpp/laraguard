<?php

namespace S4mpp\Laraguard\Tests\Unit\Navigation;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use S4mpp\Laraguard\Navigation\{Menu, MenuItem};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class MenuTest extends TestCase
{
    public function test_create_item(): void
    {
        $menu = new Menu();

        $item = $menu->createItem('Title menu', 'title-menu', 'Page title');

        $this->assertInstanceOf(MenuItem::class, $item);
        $this->assertEquals('Title menu', $item->getTitle());
        $this->assertEquals('title-menu', $item->getSlug());
    }

    public function test_add_item(): void
    {
        $menu = new Menu();

        $item = $menu->createItem('Title menu', 'title-menu', 'Page title');

        $menu->addItem($item);

        $items = $menu->getLinks();

        $this->assertIsArray($items);
        $this->assertCount(1, $items);
        $this->assertContainsOnly(MenuItem::class, $items);
    }
}
