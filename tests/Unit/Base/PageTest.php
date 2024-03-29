<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Illuminate\View\View;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Navigation\Menu;
use S4mpp\Laraguard\Base\{Module, Page, Panel};
use S4mpp\Laraguard\Navigation\Breadcrumb;

final class PageTest extends TestCase
{
    public static function pageIndexProvider()
    {
        return [
            'Blank' => ['', 'laraguard::blank'],
            'View file' => ['view-file', 'view-file'],
        ];
    }

    public function test_create_instance(): void
    {
        $page = new Page('Page title', 'page-prefix');

        $this->assertEquals('Page title', $page->getTitle());
        $this->assertEquals('page-prefix', $page->getSlug());
    }

    
    public function test_get_breadcrumb(): void
    {
        $page = new Page('Page title', 'page-prefix');

        $breadcrumb = $page->getBreadCrumb();

        $this->assertInstanceOf(Breadcrumb::class, $breadcrumb);
        $this->assertEquals('Page title', $breadcrumb->getTitle());
        $this->assertEquals('page-title', $breadcrumb->getSlug());
    }

    public function test_get_breadcrumb_without_title(): void
    {
        $page = new Page('', '');

        $breadcrumb = $page->getBreadCrumb();

        $this->assertNull($breadcrumb);
    }

    /**
     * @dataProvider pageIndexProvider
     */
    public function test_get_page_index(?string $view, string $view_to_render): void
    {
        $module = (new Module('Name Module', ''))->addIndex($view);

        $index = $module->getPage('index');

        $this->assertCount(1, $module->getPages());

        $this->assertInstanceOf(Page::class, $index);
        $this->assertEquals('', $index->getTitle());
        $this->assertNull($index->getAction());
        $this->assertEquals($view_to_render, $index->getView());
        $this->assertEquals('index', $index->getSlug());
    }

    public function test_method(): void
    {
        $page = new Page('', '');

        $add_page = $page->method('methodName');

        $this->assertInstanceOf(Page::class, $add_page);
        $this->assertEquals('methodName', $page->getMethod());
    }

    public function test_middleware(): void
    {
        $page = new Page('', '');

        $add_middleware = $page->middleware(['middleware1', 'middleware2', 'middleware3']);

        $this->assertInstanceOf(Page::class, $add_middleware);
    }

    public function test_view(): void
    {
        $page = new Page('', '');

        $add_view = $page->view('view-name');

        $this->assertInstanceOf(Page::class, $add_view);
        $this->assertEquals('view-name', $page->getView());
    }
}
