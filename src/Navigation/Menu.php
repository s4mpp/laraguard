<?php

namespace S4mpp\Laraguard\Navigation;

final class Menu
{
    private array $items = [];

    public function __construct(private $callback_get_route_name)
    {}

    public function getLinks(): array
    {
        return $this->items;
    }

    public function generate(array $modules): void
    {
        foreach ($modules as $module) {
            if (! $module->canShowInMenu()) {
                continue;
            }

            $page_index = $module->getPageIndex();

            if ($section = $module->getOnSection()) {
                $item_section = $this->items[$section->getSlug()] ?? null;

                if (! $item_section) {
                    $item_section = $this->addItem($this->createItem($section->getTitle(), $section->getSlug()));
                }

                $menu_item = $this->createItem($module->getTitle(), $module->getSlug(), $page_index?->getSlug());

                $item_section->addSubMenu($menu_item);

                continue;
            }

            $this->addItem($this->createItem($module->getTitle(), $module->getSlug(), $page_index?->getSlug()));
        }
    }

    public function activate(string $current_route): void
    {
        foreach ($this->items as $item) {
            if ($item->checkActiveByRoute($current_route)) {
                $item->activate();

                break;
            }

            foreach ($item->getSubMenuItems() as $sub_item) {
                if ($sub_item->checkActiveByRoute($current_route)) {
                    $sub_item->activate();
                    $item->activate();

                    break;
                }
            }
        }
    }

    private function addItem(MenuItem $item): MenuItem
    {
        $this->items[$item->getSlug()] = $item;

        return $item;
    }

    private function createItem($module_title, $module_slug, $page_slug = null): MenuItem
    {
        $menu_item = new MenuItem($module_title, $module_slug);

        if ($page_slug) {
            $menu_item->setRoute(call_user_func($this->callback_get_route_name, $module_slug));

            $menu_item->setAction(route(call_user_func($this->callback_get_route_name, $module_slug, $page_slug)));
        }

        return $menu_item;
    }
}
