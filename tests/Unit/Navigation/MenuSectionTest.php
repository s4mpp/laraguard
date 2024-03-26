<?php

namespace S4mpp\Laraguard\Tests\Unit\Navigation;

use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Navigation\MenuItem;
use S4mpp\Laraguard\Navigation\Breadcrumb;
use S4mpp\Laraguard\Navigation\MenuSection;
use Illuminate\Support\Facades\{Auth, Hash};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class MenuSectionTest extends TestCase
{
    public function test_create_my_account_section(): void
    {
		$my_account_section = MenuSection::myAccount();

		$this->assertInstanceOf(MenuSection::class, $my_account_section);
        
        $this->assertEquals('Minha conta', $my_account_section->getTitle());

        $this->assertEquals('minha-conta', $my_account_section->getSlug());
    }

    public function test_get_breadcrumb(): void
    {
        $menu_section = new MenuSection('Menu title', 'menu-prefix');

        $breadcrumb = $menu_section->getBreadCrumb();

        $this->assertInstanceOf(Breadcrumb::class, $breadcrumb);
        $this->assertEquals('Menu title', $breadcrumb->getTitle());
        $this->assertEquals('menu-title', $breadcrumb->getSlug());
    }

    public function test_get_breadcrumb_without_title(): void
    {
        $menu_section = new MenuSection('', '');

        $breadcrumb = $menu_section->getBreadCrumb();

        $this->assertNull($breadcrumb);
    }
}
