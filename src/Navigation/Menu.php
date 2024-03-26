<?php

namespace S4mpp\Laraguard\Navigation;

use Closure;
use S4mpp\Laraguard\Base\Module;

final class Menu
{
    /**
     * @var array<MenuItem>
     */
    private array $items = [];

    public function __construct(private ?Closure $callback_get_route_name = null)
    {
    }

    /**
     * @return array<MenuItem>
     */
    public function getLinks(): array
    {
        return $this->items;
    }

    public function activate(string $module, string $section = null): self
    {
        $items = $this->items;

        if($section)
        {
            $menu_section = $this->items[$section] ?? null;

            $menu_section?->activate();

            $items = $menu_section?->getSubMenuItems();
        }
        
        $module = $items[$module] ?? null;
        
        $module?->activate();

        return $this;
    }

    /**
     * @param array<Module> $modules
     */
    public function generate(array $modules): void
    {
        foreach ($modules as $module) {
            if (! $module->canShowInMenu()) {
                continue;
            }

            $page_index = $module->getPageIndex();

            if ($section = $module->getSection()) {
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

    private function addItem(MenuItem $item): MenuItem
    {
        $this->items[$item->getSlug()] = $item;

        return $item;
    }

    private function createItem(string $module_title, string $module_slug, ?string $page_slug = null): MenuItem
    {
        $menu_item = new MenuItem($module_title, $module_slug);

        if ($page_slug && is_callable($this->callback_get_route_name)) {
            $menu_item->setRoute(call_user_func($this->callback_get_route_name, $module_slug));

            $menu_item->setAction(route(call_user_func($this->callback_get_route_name, $module_slug, $page_slug)));
        }

        return $menu_item;
    }
}
