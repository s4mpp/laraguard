<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Utils;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Traits\TitleSluggable;
use S4mpp\Laraguard\Controllers\PageController;

final class Page
{
    use TitleSluggable;

    // private string $controller = PageController::class;

    private ?string $method = 'GET';

    private ?string $action = null;

    private ?string $view = null;

    private ?string $uri = null;

    private bool $is_index = false;

    /**
     * @var array<string>
     */
    private array $middlewares = [];

    // private bool $show_in_menu = true;

    public function __construct(private string $title, ?string $slug = null)
    {
        $this->setSlug($slug);
    }

    public function uri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function view(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function action(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function index(): self
    {
        $this->is_index = true;

        return $this;
    }

    public function isIndex(): bool
    {
        return $this->is_index;
    }

    /**
     * @param  array<mixed>  $middlewares
     */
    public function middleware(...$middlewares): self
    {
        array_push($this->middlewares, $middlewares);

        return $this;
    }

    public static function current(): ?string
    {
        return Utils::getSegmentRouteName(3);
    }

    // public function controller(string $controller, string $action = null)
    // {
    // 	$this->controller = $controller;

    // 	$this->method = $action;

    // 	return $this;
    // }

    // public function hideInMenu()
    // {
    // 	$this->show_in_menu = false;

    // 	return $this;
    // }

    // public function canShowInMenu(): bool
    // {
    // 	return $this->show_in_menu;
    // }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getView(): string
    {
        return $this->view ?? 'laraguard::blank';
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @param  array<mixed>  $data
     */
    public function render(?string $file = null, array $data = []): View|\Illuminate\Contracts\View\Factory
    {
        $file ??= $this->getView();

        $title = $this->getTitle();

        $data['home_url'] = $data['my_account_url'];

        $data['page_title'] = ! empty($title) ? $title : $data['module_title'];

        return view($file, $data);
    }

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
