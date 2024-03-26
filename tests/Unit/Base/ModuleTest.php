<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Closure;
use Illuminate\View\View;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Navigation\Menu;
use S4mpp\Laraguard\Navigation\Breadcrumb;
use S4mpp\Laraguard\Base\{Module, Page, Panel};

final class ModuleTest extends TestCase
{
    public static function hideOnMenuProvider()
    {
        return [
            'boolean' => [true],
            'closure' => [fn() => true]
        ];
    }

    public static function breadCrumbProvider()
    {
        return [
            'with route' => [true, 'lg.web.title.index'],
            'without route' => [false, null]
        ];
    }
    

    public function test_create_instance(): void
    {
        $module = new Module('Module title', 'module-prefix');

        $this->assertEquals('Module title', $module->getTitle());
        $this->assertEquals('module-prefix', $module->getSlug());
        $this->assertTrue($module->canShowInMenu());
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

    /**
     * @dataProvider hideOnMenuProvider
     */
    public function test_hide_on_menu(bool|Closure|null $value = null): void
    {
        $module = new Module('', '');

        $module->hideInMenu($value);

        $this->assertFalse($module->canShowInMenu());
    }

    public function test_is_starter(): void
    {
        $module = new Module('', '');

        $module->starter();

        $this->assertTrue($module->isStarter());
    }

    /**
     *
     * @dataProvider breadCrumbProvider
     */
    public function test_get_breadcrumb_same_name_with_page(bool $has_route, string $route = null): void
    {
        $module = new Module('Module title', 'title');

        if($has_route)
        {
            $module->addIndex();
        }

        $breadcrumb = $module->getBreadCrumb(new Panel('Panel name', '', 'web'), new Page('Page title', 'page-title'));

        $this->assertInstanceOf(Breadcrumb::class, $breadcrumb);
        $this->assertEquals('Module title', $breadcrumb->getTitle());
        $this->assertEquals('module-title', $breadcrumb->getSlug());
        $this->assertequals($route, $breadcrumb->getRoute());
    }
}
