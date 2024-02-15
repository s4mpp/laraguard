<?php

namespace S4mpp\Laraguard\Navigation;

use Closure;

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

    public function addItem(MenuItem $item): MenuItem
    {
        $this->items[$item->getSlug()] = $item;

        return $item;
    }

    public function createItem(string $module_title, string $module_slug, string $page_slug = null): MenuItem
    {
        $menu_item = new MenuItem($module_title, $module_slug);

        if ($page_slug && is_callable($this->callback_get_route_name)) {
            $menu_item->setRoute(call_user_func($this->callback_get_route_name, $module_slug));

            $menu_item->setAction(route(call_user_func($this->callback_get_route_name, $module_slug, $page_slug)));
        }

        return $menu_item;
    }
}
