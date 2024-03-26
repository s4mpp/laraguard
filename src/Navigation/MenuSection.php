<?php

namespace S4mpp\Laraguard\Navigation;

use S4mpp\Laraguard\Traits\TitleSluggable;

final class MenuSection
{
    use TitleSluggable;

    public function __construct(private string $title, ?string $slug = null)
    {
        $this->setSlug($slug);
    }

    public static function myAccount(): MenuSection
    {
        return new MenuSection('Minha conta', 'minha-conta');
    }

    public function getBreadcrumb(): ?Breadcrumb
    {
        if(!$section_title = $this->title)
        {
            return null;
        }
        
        return new Breadcrumb($section_title);
    }
}
