<?php

namespace S4mpp\Laraguard\Navigation;

use S4mpp\Laraguard\Traits\TitleSluggable;

final class MenuSection
{
    use TitleSluggable;

    public function __construct(private string $title, ?string $slug = null)
    {
        $this->slug = $slug;
    }
}
