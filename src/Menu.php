<?php

namespace Nedwors\LaravelMenu;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Menu
{
    use Macroable;

    protected const DEFAULT_MENU = 'app';
    protected const DEFAULT_FILTER = 'default-filter';

    /** @var array<string, Closure(): array<int, Item>> */
    protected array $menus = [];

    /** @var array<string, ?Closure(Item)> */
    protected array $filters = [];

    public function item(string $name): Item
    {
        return (new Item())->called($name);
    }

    /** @param Closure(): array<int, Item> $items */
    public function define(Closure $items, ?string $menu = self::DEFAULT_MENU): self
    {
        $this->items[$menu] = $items;

        return $this;
    }

    public function items(?string $menu = self::DEFAULT_MENU): Collection
    {
        return Collection::wrap(value($this->items[$menu], app(), auth()->user()))
            ->pipe($this->applyFilter($menu));
    }

    /** @param Closure(Item): mixed $filter */
    public function filter(Closure $filter, string $menu = self::DEFAULT_FILTER): self
    {
        $this->filters[$menu] = $filter;

        return $this;
    }

    public function applyFilter(string $menu): Closure
    {
        return match (true) {
            isset($this->filters[$menu]) => fn (Collection $items) => $items->filter($this->filters[$menu]),
            isset($this->filters[self::DEFAULT_FILTER]) => fn (Collection $items) => $items->filter($this->filters[self::DEFAULT_FILTER]),
            default => fn (Collection $items) => $items->filter->available()
        };
    }
}
