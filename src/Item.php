<?php

namespace Nedwors\LaravelMenu;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Fluent;
use Illuminate\Support\Traits\Macroable;

/**
 * @property      string           $name     The display name for the item
 * @property      string           $url      The full url for the item
 * @property      ?string          $heroicon The heroicon name for the item
 * @property      ?string          $icon     The icon name/path for the item
 * @property-read bool             $active    Determine if the current item is active
 * @property-read bool             $subActive Determine if any of the items decendants are active
 * @property-read Collection<self> $subItems  Define a sub menu for the item
 */
class Item extends Fluent
{
    use Macroable;

    public string $url = '#0';

    /** @var array<int, self> */
    protected array $subItemsArray = [];

    /** @var array<int, bool> */
    protected array $conditions = [];

    /** @var Closure(self): bool */
    protected ?Closure $activeCheck = null;

    /** @var Closure(self): bool */
    protected ?Closure $filter = null;

    public function called(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function for(string $route, mixed $parameters = [], bool $absolute = true): self
    {
        $this->url = Route::has($route) ? route($route, $parameters, $absolute) : $route;

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

    /** @param Closure(self): bool $activeCheck */
    public function activeWhen(Closure $activeCheck): self
    {
        $this->activeCheck = $activeCheck;

        return $this;
    }

    /** @param Closure(self): bool $filter */
    public function filterUsing(Closure $filter): self
    {
        $this->filter = $filter;

        return $this;
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
        return is_null($this->activeCheck) ? URL::current() == URL::to($this->url) : (bool) value($this->activeCheck, $this);
    }

    protected function available(): bool
    {
        return collect($this->conditions)->every(fn ($condition) => $condition === true);
    }

    /** @return Collection<int, self> */
    protected function subItems(): Collection
    {
        return collect($this->subItemsArray)
            ->when($this->filter, fn (Collection $items) => $items->filter($this->filter)->each->filterUsing($this->filter))
            ->when($this->activeCheck, fn (Collection $items) => $items->each->activeWhen($this->activeCheck));
    }

    /** @param Collection<int, self> $items */
    protected function subItemsAreActive(Collection $items): bool
    {
        return $items->reduce(fn (bool $active, self $item) => $item->subItems->isEmpty() ? $active : $this->subItemsAreActive($item->subItems),
            $items->contains->active
        );
    }
}
