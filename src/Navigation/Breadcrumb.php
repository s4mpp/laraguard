<?php

namespace S4mpp\Laraguard\Navigation;

final class Breadcrumb
{
    public function __construct(private string $title, private ?string $url = null)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
