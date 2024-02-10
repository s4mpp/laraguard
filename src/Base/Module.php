<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Utils;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Traits\TitleSluggable;

final class Module
{
    use TitleSluggable;

    private ?string $controller = null;

    private bool $show_in_menu = true;

    private bool $translate_title = false;

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

        $page->index();

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

    public function getFirstPage(): ?Page
    {
        return $this->pages['index'] ?? $this->pages[0] ?? null;
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
        return $this->getPage(Page::current())?->render($view, array_merge($data, [
            'module_title' => ($this->translate_title) ? Utils::translate($this->title) : $this->getTitle(),
        ]));
    }

    // public function getAction()
    // {
    // 	return $this->method ? [$this->controller, $this->method] : $this->controller;
    // }

    // public function getView(): ?string
    // {
    // 	return $this->view;
    // }

    // public function render(string $file = null, array $data = [])
    // {
    // 	$file = $file ?? $this->getView() ?? 'laraguard::blank';

    // 	$data['home_url'] = $data['my_account_url'];

    // 	$data['page_title'] = $this->getTitle();

    // 	return view($file, $data);
    // }

    // public function getController(): string
    // {
    // 	return $this->controller;
    // }

    // public function getMethod(): string
    // {
    // 	return $this->method;
    // }

    // public function hasController(): bool
    // {
    // 	return isset($this->controller);
    // }

    // public function getRouteName(string $panel_slug): string
    // {
    // 	return 'my-account.'.$panel_slug.'.page.'.$this->getSlug();
    // }
}
