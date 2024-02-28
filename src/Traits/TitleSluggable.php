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

    public function setSlug(?string $slug = null): void
    {
        if ($slug && ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new \Exception('Invalid slug regex ('.$slug.')');
        }

        $this->slug = $slug ? $slug : Str::slug($this->title);
    }

    public function getSlug(): string
    {
        return $this->slug ?? 'no-title';
    }
}
