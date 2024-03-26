<?php

namespace S4mpp\Laraguard\Base;

use Closure;
use Illuminate\Support\Str;
use S4mpp\Laraguard\Base\Page;
use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Navigation\Menu;
use S4mpp\Laraguard\Traits\HasMiddleware;
use S4mpp\Laraguard\Traits\TitleSluggable;
use S4mpp\Laraguard\Controllers\PersonalDataController;
use S4mpp\Laraguard\Controllers\PasswordChangeController;
use S4mpp\Laraguard\Navigation\{Breadcrumb, MenuSection};

final class Module
{
    use TitleSluggable, HasMiddleware;

    private ?string $controller = null;

    private bool|Closure $hide_in_menu = false;

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

    public static function changePersonalData(): self
    {
        $my_account = (new self('Meus dados', 'meus-dados'))
            ->controller(PersonalDataController::class)
            ->addIndex();

        $my_account->createPage('', 'salvar-dados', 'save-personal-data')->method('PUT')->action('save');

        return $my_account;
    }

    public static function changePassword(): self
    {
        $change_password = (new self('Alterar senha', 'alterar-senha'))
            ->controller(PasswordChangeController::class)
            ->addIndex();

        $change_password->createPage('', 'salvar-senha', 'save-password')->method('PUT')->action('save');

        return $change_password;
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
        $page = $this->createPage('', '/', 'index');

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

    public function getSection(): ?MenuSection
    {
        return $this->section;
    }

    public function getPrefixUrl(): string
    {
        return implode('/', [$this->section?->getSlug(), $this->getSlug()]);
    }

    public function getBreadcrumb(Panel $panel, Page $page): ?Breadcrumb
    {
        $module_page_index_slug = $this->getPageIndex()?->getSlug();
        
        if($module_page_index_slug && $module_page_index_slug != $page->getSlug())
        {
            $route_module = $panel->getRouteName($this->getSlug(), $module_page_index_slug);
        }
                
        return new Breadcrumb($this->getTitle(), $route_module ?? null);
    }

    public function createPage(string $title, ?string $uri = null, ?string $slug = null): Page
    {
        $slug_title = Str::slug($title);

        $uri = ($uri ?? $slug_title);

        $slug = ($slug ?? $slug_title);

        $page = (new Page($title, $slug))->uri($uri);

        $this->addPage($page);

        return $page;
    }

    public function addPage(Page $page): Page
    {
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
        return(is_callable($this->hide_in_menu))
            ? !call_user_func($this->hide_in_menu)
            : !$this->hide_in_menu;
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
    // public function getLayout(?string $view = null, Menu $menu, array $data = []): null|View|\Illuminate\Contracts\View\Factory
    // {
    //     if ($this->section) {
    //         $breadcrumb = new Breadcrumb($this->section->getTitle());
    //         $breadcrumbs[$breadcrumb->getSlug()] = $breadcrumb;
    //     }
        
    //     $module_title = $this->getTitle();

    //     if($module_title)
    //     {
    //         $breadcrumb = new Breadcrumb($module_title, $data['panel']->getRouteName($this->getSlug, 'index'));
    //         $breadcrumbs[$breadcrumb->getSlug()] = $breadcrumb;
    //     }

    //     $menu->activate($this->getSlug(), $this->section?->getSlug());

    //     return $this->getPage(Page::current())?->render($view, $menu, array_merge($data, [
    //         'section_title' => $this->section->getTitle(),
    //         'module_title' => $module_title,
    //         'breadcrumbs' => $breadcrumbs ?? []
    //     ]));
    // }
}
