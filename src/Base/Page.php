<?php

namespace S4mpp\Laraguard\Base;

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

	private array $middlewares = [];

	// private bool $show_in_menu = true;

	public function __construct(private string $title, string $slug = null)
	{
		$this->setSlug($slug);
	}

	public function uri(string $uri)
	{
		$this->uri = $uri;

		return $this;
	}

	public function view(string $view)
	{
		$this->view = $view;

		return $this;
	}

	public function action(string $action)
	{
		$this->action = $action;

		return $this;
	}

	public function method(string $method)
	{
		$this->method = $method;

		return $this;
	}

	public function index()
	{
		$this->is_index = true;

		return $this;
	}
	
	public function isIndex(): bool
	{
		return $this->is_index;
	}

	public function middleware(...$middlewares)
	{
		$this->middleware = $middlewares;

		return $this;
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

	public function render(string $file = null, array $data = [])
	{
		$file = $file ?? $this->getView();

		$title = $this->getTitle();

		$data['home_url'] = $data['my_account_url'];

		$data['page_title'] = !empty($title) ? $title : $data['module_title'];

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