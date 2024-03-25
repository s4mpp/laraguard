<?php

namespace S4mpp\Laraguard\Base;

use Closure;
use Illuminate\Support\Str;
use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Navigation\Menu;
use S4mpp\Laraguard\Traits\HasMiddleware;
use S4mpp\Laraguard\Traits\TitleSluggable;
use S4mpp\Laraguard\Navigation\{Breadcrumb, MenuSection};

final class Module
{
    use TitleSluggable, HasMiddleware;

    private ?string $controller = null;

    private bool|Closure $hide_in_menu = false;

    private bool $translate_title = false;

    private ?MenuSection $section = null;

    private bool $is_starter = false;

    /**
     * @var array<Page>
     */
    private array $pages = [];

    public function __construct(private string $title, ?string $slug = null)
    {
        $this->setSlug($slug);
    }

    public function starter(): self
    {
        $this->is_starter = true;

        return $this;
    }

    public function isStarter(): bool
    {
        return $this->is_starter;
    }

    public function addIndex(?string $view = null): self
    {
        $page = $this->addPage('', '/', 'index');

        if ($view) {
            $page->view($view);
        }

        $page->isIndex();

        return $this;
    }

    public function onSection(MenuSection $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function controller(string $controller): self
    {
        $this->controller = $controller;

        return $this;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function getOnSection(): ?MenuSection
    {
        return $this->section;
    }

    public function getPrefixUrl(): string
    {
        return implode('/', [$this->section?->getSlug(), $this->getSlug()]);
    }

    public function translateTitle(): self
    {
        $this->translate_title = true;

        return $this;
    }

    public function addPage(string $title, ?string $uri = null, ?string $slug = null): Page
    {
        $slug_title = Str::slug($title);

        $uri = ($uri ?? $slug_title);

        $slug = ($slug ?? $slug_title);

        $page = (new Page($title, $slug))->uri($uri);

        $this->pages[$page->getSlug()] = $page;

        return $page;
    }

    /**
     * @return array<Page>
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    public function getPageIndex(): ?Page
    {
        foreach ($this->getPages() as $page) {
            if ($page->getIsIndex()) {
                return $page;
            }
        }

        return null;
    }



    public function getPage(?string $page_name = null): ?Page
    {
        return $this->pages[$page_name] ?? null;
    }

    public function hideInMenu(bool|Closure $value = true): self
    {
        

        $this->hide_in_menu = $value;

        return $this;
    }

    public function canShowInMenu(): bool
    {
        if(is_callable($this->hide_in_menu))
        {
            $hide_in_menu = call_user_func($this->hide_in_menu);
        }
        else
        {
            $hide_in_menu = $this->hide_in_menu;
        }

        return !$hide_in_menu;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function current(): ?string
    {
        return Utils::getSegmentRouteName(2);
    }

    /**
     * @codeCoverageIgnore
     * @param  array<mixed>  $data
     */
    public function getLayout(?string $view = null, Menu $menu, array $data = []): null|View|\Illuminate\Contracts\View\Factory
    {
        $data['breadcrumbs'] = [];
        
        if ($this->section) {
            $breadcrumbs[] = new Breadcrumb($this->section->getTitle());
        }
        
        /** @var string $module_title */
        $module_title = ($this->translate_title) ? __($this->title) : $this->getTitle();

        if($module_title)
        {
            $breadcrumbs[] = new Breadcrumb($module_title);
        }

        $menu->activate($this->getSlug(), $this->section?->getSlug());

        return $this->getPage(Page::current())?->render($view, $menu, array_merge($data, [
            'module_title' => $module_title,
        ]));
    }
}
