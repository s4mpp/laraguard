<?php
namespace S4mpp\Laraguard\Traits;

use Illuminate\Support\Str;

trait TitleSluggable
{
	private ?string $slug = null;

	public function getTitle()
	{
		return $this->title;
	}

	public function getSlug(): string
	{
		return $this->slug ? $this->slug : Str::slug($this->title);
	}
}