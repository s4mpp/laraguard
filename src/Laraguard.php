<?php

namespace S4mpp\Laraguard;

use S4mpp\Laraguard\Base\Page;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Base\Module;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Navigation\Breadcrumb;

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
    public static function layout(?string $file = null, array $data = []): null|View|\Illuminate\Contracts\View\Factory
    {
        $panel = self::getPanel(Panel::current());

        $module = $panel?->getModule(Module::current());

        $page = $module?->getPage(Page::current());

        if(!$panel || !$module || !$page)
        {
            return null;
        }

        $breadcrumbs = array_filter([
            $module->getSection()?->getBreadcrumb(),
            $module->getBreadcrumb($panel, $page),
            $page->getBreadcrumb()
        ]);
         
        $menu = $panel->generateMenu()->activate($module->getSlug(), $module->getSection()?->getSlug());

        $links = $menu->getLinks();

        $page_title = $page->getTitle();

        return view($file ??= $page->getView(), array_merge($data, [
            'panel' => $panel,
            'menu_links' => $links,
            'breadcrumbs' => $breadcrumbs,
            'guard_name' => $panel->getGuardName(),
            'home_url' => ($route_home = current($links)) ? route($panel->getRouteName($route_home->getSlug(), 'index')) : null,
            'logout_url' => route($panel->getRouteName('signout')),
            'page_title' => !empty($page_title) ? $page_title : $module->getTitle()
        ]));
    }
}
