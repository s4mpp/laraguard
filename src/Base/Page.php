<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Traits\TitleSluggable;
use S4mpp\Laraguard\Controllers\PageController;

final class Page
{	
	use TitleSluggable;

	// private string $controller = PageController::class;

	private ?string $method = null;
	
	private ?string $view = null;

	// private bool $show_in_menu = true;

	public function __construct(private string $title, private string $uri, string $slug)
	{
		$this->setSlug($slug);

		return $this;
	}
	

	// public function controller(string $controller, string $method = null)
	// {
	// 	$this->controller = $controller;

	// 	$this->method = $method;

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

	public function getMethod(): ?string
	{
		return $this->method;
	}

	public function getView(): ?string
	{
		return $this->view;
	}

	public function render(string $file = null, array $data = [])
	{
		$file = $file ?? $this->getView() ?? 'laraguard::blank';

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