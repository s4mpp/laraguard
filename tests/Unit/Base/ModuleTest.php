<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Illuminate\View\View;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Navigation\Menu;
use S4mpp\Laraguard\Base\{Module, Page, Panel};

final class ModuleTest extends TestCase
{
    public function test_create_instance(): void
    {
        $module = new Module('Module title', 'module-prefix');

        $this->assertEquals('Module title', $module->getTitle());
        $this->assertEquals('module-prefix', $module->getSlug());
    }

    public function test_get_page_on_creating_module(): void
    {
        $module = new Module('', '');

        $this->assertNull($module->getPage(null));
    }

    public function test_get_page_index(): void
    {
        $module = (new Module('', ''))->addIndex();

        $index = $module->getPage('index');

        $this->assertCount(1, $module->getPages());
    }

    public function test_hide_on_menu(): void
    {
        $module = new Module('', '');

        $module->hideInMenu();

        $this->assertFalse($module->canShowInMenu());
    }

    public function test_show_in_menu(): void
    {
        $module = new Module('', '');

        $this->assertTrue($module->canShowInMenu());
    }

    public function test_is_starter(): void
    {
        $module = new Module('', '');

        $module->starter();

        $this->assertTrue($module->isStarter());
    }
}
