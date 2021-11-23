<?php

namespace Nedwors\Navigator;

use Closure;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\Macroable;

class Nav
{
    use Macroable;

    public const DEFAULT = 'menu.default';

    /** @var array<string, Closure(): iterable<int, Item>> */
    protected array $itemsArray = [];

    /** @var array<string, Closure(Item): bool> */
    protected array $activeChecks = [];

    /** @var array<string, Closure(Item): bool> */
    protected array $filters = [];

    /** @var class-string */
    protected string $itemClass = Item::class;

    /** @param class-string $class */
    public function using(string $class): self
    {
        $this->itemClass = $class;

        return $this;
    }

    public function item(string $name): Item
    {
        return resolve($this->itemClass)->called($name);
    }

    /** @param Closure(): iterable<int, Item> $items */
    public function define(Closure $items, string $menu = self::DEFAULT): self
    {
        $this->itemsArray[$menu] = $items;

        return $this;
    }

    public function toJson(string $menu = self::DEFAULT, mixed $options = 0): string
    {
        return $this->items($menu)->toJson($options);
    }

    /** @return LazyCollection<Item> */
    public function items(string $menu = self::DEFAULT): LazyCollection
    {
        return LazyCollection::make(value($this->itemsArray[$menu] ?? [], auth()->user()))
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
            isset($this->activeChecks[$menu]) => fn (LazyCollection $items) => $items->each->activeWhen($this->activeChecks[$menu]),
            isset($this->activeChecks[self::DEFAULT]) => fn (LazyCollection $items) => $items->each->activeWhen($this->activeChecks[self::DEFAULT]),
            default => fn (LazyCollection $items) => $items
        };
    }

    protected function applyFilter(string $menu): Closure
    {
        $filter = match (true) {
            isset($this->filters[$menu]) => $this->filters[$menu],
            isset($this->filters[self::DEFAULT]) => $this->filters[self::DEFAULT],
            default => fn (Item $item) => $item->available,
        };

        return fn (LazyCollection $items) => $items->filter($filter)->each->filterSubItemsUsing($filter);
    }
}
