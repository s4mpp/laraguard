<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Utils;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Traits\TitleSluggable;
use S4mpp\Laraguard\Navigation\{Breadcrumb, MenuSection};

final class Module
{
    use TitleSluggable;

    private ?string $controller = null;

    private bool $show_in_menu = true;

    private bool $translate_title = false;

    private ?MenuSection $section = null;

    /**
     * @var array<Page>
     */
    private array $pages = [];

    public function __construct(private string $title, ?string $slug = null)
    {
        $this->setSlug($slug);
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

    public static function current(): ?string
    {
        return Utils::getSegmentRouteName(2);
    }

    public function getPage(?string $page_name = null): ?Page
    {
        return $this->pages[$page_name] ?? null;
    }

    public function hideInMenu(): self
    {
        $this->show_in_menu = false;

        return $this;
    }

    public function canShowInMenu(): bool
    {
        return $this->show_in_menu;
    }

    /**
     * @param  array<mixed>  $data
     */
    public function getLayout(?string $view = null, array $data = []): null|View|\Illuminate\Contracts\View\Factory
    {
        $module_title = ($this->translate_title) ? Utils::translate($this->title) : $this->getTitle();

        if ($this->section) {
            Breadcrumb::add(new Breadcrumb($this->section->getTitle()));
        }

        Breadcrumb::add(new Breadcrumb(new Breadcrumb($module_title)));

        return $this->getPage(Page::current())?->render($view, array_merge($data, [
            'module_title' => $module_title,
        ]));
    }
}
