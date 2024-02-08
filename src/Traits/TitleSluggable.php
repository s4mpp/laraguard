<?php
namespace S4mpp\Laraguard\Traits;

use Illuminate\Support\Str;

trait TitleSluggable
{
	private ?string $slug = null;

	public function getTitle(): string
	{
		return $this->title ?? 'No title';
	}

	public function setSlug(string $slug = null): void
	{
		$this->slug = $slug ? $slug : Str::slug($this->title);
	}

	public function getSlug(): string
	{
		return $this->slug ?? 'no-title';
	}
}