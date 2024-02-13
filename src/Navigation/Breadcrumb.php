<?php

namespace S4mpp\Laraguard\Navigation;

final class Breadcrumb
{
    private static array $breadcrumbs = [];

    public function __construct(private string $title, private ?string $url = null)
    {}

    public static function add(Breadcrumb $breadcrumb): void
    {
        if(array_key_exists($breadcrumb->title, self::$breadcrumbs))
        {
            return;
        }

        self::$breadcrumbs[$breadcrumb->title] = $breadcrumb;
    }

    public function __toString()
    {
        return $this->title;
    }

    public static function getBreadcrumbs(): array
    {
        return self::$breadcrumbs;
    }
}
