<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Utils;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Navigation\Breadcrumb;
use S4mpp\Laraguard\Traits\TitleSluggable;

final class Page
{
    use TitleSluggable;

    private ?string $method = 'GET';

    private ?string $action = null;

    private ?string $view = null;

    private ?string $uri = null;

    private bool $is_index = false;

    /**
     * @var array<string>
     */
    private array $middlewares = [];

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

    public function isIndex(): self
    {
        $this->is_index = true;

        return $this;
    }

    public function getIsIndex(): bool
    {
        return $this->is_index;
    }

    /**
     * @param  array<mixed>  $middlewares
     */
    public function middleware(array $middlewares): self
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public static function current(): ?string
    {
        return Utils::getSegmentRouteName(3);
    }

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

        $data['home_url'] = $data['my_account_url'];

        $title = $this->getTitle();

        $title = ! empty($title) ? $title : $data['module_title'];

        $data['page_title'] = $title;

        // $data['breadcrumbs'][] = new Breadcrumb($title);

        Breadcrumb::add(new Breadcrumb($title ?? ''));

        $data['breadcrumbs'] = Breadcrumb::getBreadcrumbs();

        return view($file, $data);
    }
}
