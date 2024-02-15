<?php

namespace S4mpp\Laraguard\Navigation;

use S4mpp\Laraguard\Traits\TitleSluggable;

final class MenuItem
{
    use TitleSluggable;

    private string $action = '#';

    private bool $is_active = false;

    /**
     * @var array<MenuItem>
     */
    private array $sub_menu_items = [];

    private ?string $route = null;

    public function __construct(private string $title, ?string $slug = null)
    {
        $this->slug = $slug;
    }

    public function hasSubMenu(): bool
    {
        return count($this->sub_menu_items) > 0;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function addSubMenu(MenuItem $item): self
    {
        $this->sub_menu_items[] = $item;

        return $this;
    }

    /**
     * @return array<MenuItem>
     */
    public function getSubMenuItems(): array
    {
        return $this->sub_menu_items;
    }

    public function activate(): void
    {
        $this->is_active = true;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function checkActiveByRoute(string $current_route): bool
    {
        $route = $this->getRoute();

        return $route && (mb_strpos($current_route, $route) !== false);
    }
}
