<?php

namespace Nedwors\LaravelMenu;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Menu
{
    use Macroable;

    public const DEFAULT = 'default-value-for-menu-items';
    public const DEFAULT_MENU = 'app';

    /** @var array<string, Closure(): array<int, Item>> */
    protected array $items = [];

    /** @var array<string, Closure(Item): Collection<Item>> */
    protected array $filters = [];

    /** @var array<string, Closure(Item): bool> */
    protected array $activeChecks = [];

    public function item(string $name): Item
    {
        return (new Item())->called($name);
    }

    /** @param Closure(): array<int, Item> $items */
    public function define(Closure $items, string $menu = self::DEFAULT_MENU): self
    {
        $this->items[$menu] = $items;

        return $this;
    }

    public function items(string $menu = self::DEFAULT_MENU): Collection
    {
        return Collection::wrap(value($this->items[$menu], app(), auth()->user()))
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
        return match (true) {
            isset($this->filters[$menu]) => fn (Collection $items) => $items->filter($this->filters[$menu])->each->filterUsing($this->filters[$menu]),
            isset($this->filters[self::DEFAULT]) => fn (Collection $items) => $items->filter($this->filters[self::DEFAULT])->each->filterUsing($this->filters[self::DEFAULT]),
            default => fn (Collection $items) => $items->filter->available->each->filterUsing(fn (Item $item) => $item->available),
        };
    }
}
