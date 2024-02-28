<?php

namespace S4mpp\Laraguard\Tests\Unit\Navigation;

use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Navigation\MenuItem;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class MenuItemTest extends TestCase
{
    public function test_creation_menu(): void
    {
        $menu = new MenuItem('Title menu', 'title-menu');

        $this->assertEquals('Title menu', $menu->getTitle());

        $this->assertEquals('title-menu', $menu->getSlug());

        $this->assertEquals('#', $menu->getAction());

        $this->assertFalse($menu->isActive());
    }

    public function test_activation_menu(): void
    {
        $menu = new MenuItem('', '');

        $menu->activate();

        $this->assertTrue($menu->isActive());
    }

    public function test_check_activation(): void
    {
        $menu = new MenuItem('', '');

        $test = $menu->checkActiveByRoute('test');

        $this->assertFalse($test);
    }

    public function test_action_menu(): void
    {
        $menu = new MenuItem('', '');

        $menu->setAction('test');

        $this->assertEquals('test', $menu->getAction());
    }

    public function test_route_menu(): void
    {
        $menu = new MenuItem('', '');

        $menu->setRoute('test_route');

        $this->assertEquals('test_route', $menu->getRoute());
    }

    public function test_add_submenu(): void
    {
        $menu = new MenuItem('', '');

        $menu->addSubMenu(new MenuItem('', ''));

        $submenu_items = $menu->getSubMenuItems();

        $this->assertTrue($menu->hasSubMenu());
        $this->assertIsArray($submenu_items);
        $this->assertCount(1, $submenu_items);
    }
}
