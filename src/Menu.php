<?php

namespace Nedwors\LaravelMenu;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Menu
{
    use Macroable;

    public const DEFAULT_MENU = 'app';
    public const DEFAULT_FILTER = 'default-filter';
    public const DEFAULT_ACTIVE_CHECK = 'default-active-check';

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
    public function filter(Closure $filter, string $menu = self::DEFAULT_FILTER): self
    {
        $this->filters[$menu] = $filter;

        return $this;
    }

    /** @param Closure(Item): bool $activeCheck */
    public function active(Closure $activeCheck, string $menu = self::DEFAULT_ACTIVE_CHECK): self
    {
        $this->activeChecks[$menu] = $activeCheck;

        return $this;
    }

    protected function injectActiveCheck(string $menu): ?Closure
    {
        return match (true) {
            isset($this->activeChecks[$menu]) => fn (Collection $items) => $items->each->activeWhen($this->activeChecks[$menu]),
            isset($this->activeChecks[self::DEFAULT_ACTIVE_CHECK]) => fn (Collection $items) => $items->each->activeWhen($this->activeChecks[self::DEFAULT_ACTIVE_CHECK]),
            default => fn (Collection $items) => $items
        };
    }

    protected function applyFilter(string $menu): Closure
    {
        return match (true) {
            isset($this->filters[$menu]) => fn (Collection $items) => $items->filter($this->filters[$menu]),
            isset($this->filters[self::DEFAULT_FILTER]) => fn (Collection $items) => $items->filter($this->filters[self::DEFAULT_FILTER]),
            default => fn (Collection $items) => $items->filter->available
        };
    }
}
