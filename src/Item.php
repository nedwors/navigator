<?php

namespace Nedwors\LaravelMenu;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Fluent;
use Illuminate\Support\Traits\Macroable;

/**
 * @property      string  $name
 * @property      string  $url
 * @property      ?string $heroicon
 * @property      ?string $icon
 * @property-read bool    $active
 * @property-read Collection<self>    $subItems
 */
class Item extends Fluent
{
    use Macroable;

    public string $url = '#0';

    /** @var array<int, self> */
    protected array $subItemsArray = [];

    /** @var array<int, bool> */
    protected array $conditions = [];

    public function called(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function for(string $route, mixed $parameters = []): self
    {
        $this->url = Route::has($route) ? route($route, $parameters) : $route;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function heroicon(string $heroicon): self
    {
        $this->heroicon = $heroicon;

        return $this;
    }

    public function subMenu(self ...$items): self
    {
        $this->subItemsArray = $items;

        return $this;
    }

    public function when(?bool $condition): self
    {
        $this->conditions[] = (bool) $condition;

        return $this;
    }

    public function unless(?bool $condition): self
    {
        return $this->when(!(bool) $condition);
    }

    /** @param mixed $name */
    public function __get($name): mixed
    {
        return match ($name) {
            'active' => $this->active(),
            'available' => $this->available(),
            'subItems' => $this->subItems(),
            'subActive' => $this->subItemsAreActive($this->subItems),
            default => parent::__get($name)
        };
    }

    protected function active(): bool
    {
        return URL::current() == URL::to($this->url);
    }

    protected function available(): bool
    {
        return collect($this->conditions)->every(fn ($condition) => $condition === true);
    }

    /** @return Collection<int, self> */
    protected function subItems(): Collection
    {
        return collect($this->subItemsArray);
    }

    /** @param Collection<int, self> $items */
    protected function subItemsAreActive(Collection $items, bool $active = false): bool
    {
        if ($items->contains->active) {
            return true;
        }

        return $items->reduce(fn (bool $carry, self $item) =>
            $item->subItems->isEmpty() ? $carry : $this->subItemsAreActive($item->subItems, $carry)
        , $active);
    }

}
