<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Navigation\Menu;
use S4mpp\Laraguard\Traits\HasMiddleware;
use S4mpp\Laraguard\Navigation\Breadcrumb;
use S4mpp\Laraguard\Traits\TitleSluggable;

final class Page
{
    use TitleSluggable, HasMiddleware;

    private ?string $method = 'GET';

    private ?string $action = null;

    private ?string $view = null;

    private ?string $uri = null;

    private bool $is_index = false;

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
     * @codeCoverageIgnore
     */
    public static function current(): ?string
    {
        return Utils::getSegmentRouteName(3);
    }

    public function getBreadCrumb(): ?Breadcrumb
    {
        if(!$page_title = $this->title)
        {
            return null;
        }
        
        return new Breadcrumb($page_title);
    }

    /**
     * @param  array<mixed>  $data
     */
    // public function render(?string $file = null, Menu $menu = null, array $data = []): View|\Illuminate\Contracts\View\Factory
    // {
    //     $file ??= $this->getView();
        
    //     $title = $this->getTitle();
        
    //     $title = ! empty($title) ? $title : $data['module_title'];
        
    //     $data['page_title'] = $title;

    //     $breadcrumb = new Breadcrumb($title ?? '');
    //     $data['breadcrumbs'][$breadcrumb->getSlug()] = $breadcrumb;
        
    //     $data['menu'] = $menu?->getLinks();  

    //     $data['home_url'] = array_keys($data['menu'])[0] ?? null;

    //     dump($data['breadcrumbs']);

    //     return view($file, $data);
    // }
}
