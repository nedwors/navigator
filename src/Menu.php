<?php

namespace Nedwors\LaravelMenu;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Menu
{
    use Macroable;

    /** @var Closure(): array<int, Item> */
    protected Closure $items;

    /** @var ?Closure(Item) */
    protected ?Closure $filter = null;

    public function item(string $name): Item
    {
        return (new Item())->called($name);
    }

    /** @param Closure(): array<int, Item> $items */
    public function define(Closure $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function items(): Collection
    {
        return Collection::wrap(value($this->items, app(), auth()->user()))
            ->pipe($this->applyFilter());
    }

    /** @param Closure(Item): mixed $filter */
    public function filter(Closure $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function applyFilter(): Closure
    {
        return fn (Collection $items) => is_null($this->filter)
            ? $items->filter->available()
            : $items->filter($this->filter);
    }
}
