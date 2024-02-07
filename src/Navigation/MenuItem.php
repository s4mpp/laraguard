<?php

namespace S4mpp\Laraguard\Navigation;

use S4mpp\Laraguard\Traits\TitleSluggable;


final class MenuItem
{
	use TitleSluggable;

	private string $action = '#';

	private bool $is_active = false;
	
	public function __construct(private string $title, string $slug = null)
	{
		$this->slug = $slug;
	}

	public function setAction(string $action): self
	{
		$this->action = $action;

		return $this;
	}

	public function getAction(): string
	{
		return $this->action;
	}

	// use Slugable, Ordenable, Titleable;

	// private $is_active = false;

	// private ?string $route = null;
	
	// private $target = null;

	// public function __construct(private string $title)
	// {
	// // 	$this->createSlug($title);

	// // 	$this->route('admin.'.$this->slug);

	// }

	// public function route(string $route)
	// {
	// 	$this->route = $route;

	// 	return $this;
	// }

	// public function target(array | string $target)
	// {
	// 	$this->target = $target;

	// 	return $this;
	// }

	public function activate(): void
	{
		$this->is_active = true;
	}

	// public function getTarget(): array | string
	// {
	// 	return $this->target;
	// }

	public function isActive(): bool
	{
		return $this->is_active;
	}


	// public function getRoute()
	// {
	// 	return $this->route;
	// }
}