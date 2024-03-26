<?php

namespace S4mpp\Laraguard\Navigation;

use Illuminate\Support\Str;
use S4mpp\Laraguard\Base\Page;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Base\Module;
use Symfony\Component\Stopwatch\Section;
use S4mpp\Laraguard\Traits\TitleSluggable;

final class Breadcrumb
{
    use TitleSluggable;
    
    public function __construct(private string $title, private ?string $route = null)
    {
        $this->slug = Str::slug($title);
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

}
