<?php

namespace S4mpp\Laraguard\Tests\Unit\Navigation;

use S4mpp\Laraguard\Base\Module;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Navigation\MenuSection;
use Illuminate\Support\Facades\{Auth, Hash};
use S4mpp\Laraguard\Navigation\{Menu, MenuItem};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class MenuTest extends TestCase
{
    public function test_generate_menu(): void
    {
        $menu = new Menu();

        $module = (new Module('Title module', 'title-module'));

        $menu->generate([$module]); 
        
        $links = $menu->getLinks();
        
        $first_item = array_values($links)[0];
        
        $this->assertIsArray($links);
        $this->assertCount(1, $links);
        
        $this->assertEquals('Title module', $first_item->getTitle());
        $this->assertEquals('title-module', $first_item->getSlug());
        $this->assertEquals('#', $first_item->getAction());
        $this->assertNull($first_item->getRoute());
        $this->assertEmpty($first_item->getSubMenuItems());
        $this->assertFalse($first_item->isActive());
    }

    public function test_generate_menu_with_route(): void
    {
        $menu = new Menu(fn() => 'welcome');

        $module = (new Module('Title module', 'title-module'))->addIndex();

        $menu->generate([$module]); 
        
        $links = $menu->getLinks();
        
        $first_item = array_values($links)[0];

        $this->assertEquals('http://localhost', $first_item->getAction());
        $this->assertEquals('welcome', $first_item->getRoute());
    }

    public function test_item_cannot_show(): void
    {
        $menu = new Menu();

        $module = (new Module('Title module', 'title-module'))->hideInMenu();

        $menu->generate([$module]); 
        
        $links = $menu->getLinks();
        
        $this->assertIsArray($links);
        $this->assertCount(0, $links);
    }

    public function test_activation_menu_item(): void
    {
        $menu = new Menu();

        $module = (new Module('Title module', 'title-module'));

        $menu->generate([$module]); 

        $menu->activate('title-module');
        
        $links = $menu->getLinks();
        
        $first_item = array_values($links)[0];
        
        $this->assertTrue($first_item->isActive());
    }

    public function test_activation_menu_section(): void
    {
        $menu = new Menu();

        $menu_section = new MenuSection('Title section', 'title-section');
        
        $module = (new Module('Title module', 'title-module'))->onSection($menu_section);

        $menu->generate([$module]); 

        $menu->activate('title-module', 'title-section');
        
        $links = $menu->getLinks();
        
        $first_item = array_values($links)[0];
        
        $first_sub_item = array_values($first_item->getSubMenuItems())[0];
        
        $this->assertTrue($first_item->isActive());
        $this->assertTrue($first_sub_item->isActive());
    }
}
