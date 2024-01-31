<?php
namespace S4mpp\Laraguard\Traits;

use Illuminate\Support\Str;

trait TitleSluggable
{
	private string $slug;

	public function getTitle()
	{
		return $this->title;
	}

	public function setSlug(string $slug = null)
	{
		$this->slug = $slug ? $slug : Str::slug($this->title);
	}

	public function getSlug(): string
	{
		return $this->slug;
	}
}