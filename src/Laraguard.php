<?php

namespace S4mpp\Laraguard;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Contracts\View\View;

final class Laraguard
{
    /**
     * @var array<Panel>
     */
    private static array $panels = [];

    public static function panel(string $title, string $prefix = '', string $guard = 'web'): Panel
    {
        $panel = new Panel($title, $prefix, $guard);

        self::$panels[$guard] = $panel;

        return $panel;
    }

    /**
     * @return array<Panel>
     */
    public static function getPanels(): array
    {
        return self::$panels;
    }

    public static function getPanel(?string $panel_name = null): ?Panel
    {
        return self::$panels[$panel_name] ?? null;
    }

    /**
     * @param  array<mixed>  $data
     */
    public static function layout(?string $view = null, array $data = []): null|View|\Illuminate\Contracts\View\Factory
    {
        return self::getPanel(Panel::current())?->getLayout($view, $data);
    }
}
