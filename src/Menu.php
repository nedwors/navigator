<?php

namespace Nedwors\LaravelMenu;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Menu
{
    use Macroable;

    public const DEFAULT = 'default-menu-item';
    public const DEFAULT_MENU = 'app';

    /** @var array<string, Closure(): array<int, Item>> */
    protected array $itemsArray = [];

    /** @var array<string, Closure(Item): bool> */
    protected array $activeChecks = [];

    /** @var array<string, Closure(Item): bool> */
    protected array $filters = [];

    public function item(string $name): Item
    {
        return (new Item())->called($name);
    }

    /** @param Closure(): array<int, Item> $items */
    public function define(Closure $items, string $menu = self::DEFAULT_MENU): self
    {
        $this->itemsArray[$menu] = $items;

        return $this;
    }

    public function items(string $menu = self::DEFAULT_MENU): Collection
    {
        return Collection::wrap(value($this->itemsArray[$menu], app(), auth()->user()))
            ->pipe($this->injectActiveCheck($menu))
            ->pipe($this->applyFilter($menu));
    }

    /** @param Closure(Item): mixed $filter */
    public function filter(Closure $filter, string $menu = self::DEFAULT): self
    {
        $this->filters[$menu] = $filter;

        return $this;
    }

    /** @param Closure(Item): bool $activeCheck */
    public function activeWhen(Closure $activeCheck, string $menu = self::DEFAULT): self
    {
        $this->activeChecks[$menu] = $activeCheck;

        return $this;
    }

    protected function injectActiveCheck(string $menu): Closure
    {
        return match (true) {
            isset($this->activeChecks[$menu]) => fn (Collection $items) => $items->each->activeWhen($this->activeChecks[$menu]),
            isset($this->activeChecks[self::DEFAULT]) => fn (Collection $items) => $items->each->activeWhen($this->activeChecks[self::DEFAULT]),
            default => fn (Collection $items) => $items
        };
    }

    protected function applyFilter(string $menu): Closure
    {
        $filter = match (true) {
            isset($this->filters[$menu]) => $this->filters[$menu],
            isset($this->filters[self::DEFAULT]) => $this->filters[self::DEFAULT],
            default => fn (Item $item) => $item->available,
        };

        return fn (Collection $items) => $items->filter($filter)->each->filterSubMenuUsing($filter);
    }
}
