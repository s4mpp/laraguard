<?php

namespace S4mpp\Laraguard\Navigation;

use S4mpp\Laraguard\Traits\TitleSluggable;
use S4mpp\Laraguard\Controllers\PageController;

final class Page
{	
	use TitleSluggable;

	private string $controller = PageController::class;

	private ?string $method = null;

	private bool $show_in_menu = true;

	public function __construct(private string $title, private ?string $view = null, string $slug = null)
	{
		$this->slug = $slug;

		return $this;
	}

	public function controller(string $controller, string $method = null)
	{
		$this->controller = $controller;

		$this->method = $method;

		return $this;
	}

	public function hideInMenu()
	{
		$this->show_in_menu = false;

		return $this;
	}

	public function canShowInMenu(): bool
	{
		return $this->show_in_menu;
	}

	public function getAction()
	{
		return $this->method ? [$this->controller, $this->method] : $this->controller;
	}

	public function getView(): ?string
	{
		return $this->view;
	}

	public function render(string $file = null, array $data = [])
	{
		$file = $file ?? $this->getView() ?? 'laraguard::blank';

		$data['home_url'] = $data['my_account_url'];

		$data['page_title'] = $this->getTitle();

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