<?php

namespace Nedwors\LaravelMenu;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Fluent;
use Illuminate\Support\LazyCollection;

/**
 * @property      string               $name     The display name for the item
 * @property      string               $url      The full url for the item
 * @property      ?string              $heroicon The heroicon name for the item
 * @property      ?string              $icon     The icon name/path for the item
 * @property-read bool                 $active    Determine if the current item is active
 * @property-read bool                 $available Determine if the current item passes its conditions for display
 * @property-read LazyCollection<self> $subItems  Retrieve the item's sub menu items
 * @property-read bool                 $subActive Determine if any of the item's decendants are active
 */
abstract class CoreItem extends Fluent
{
    public string $url = '#0';

    /** @var Closure(): iterable<int, self>|iterable<int, self> */
    protected Closure|iterable $decendants = [];

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

    /** @param Closure(): iterable<int, self>|iterable<int, self> $items */
    public function subMenu(Closure|iterable $items): self
    {
        $this->decendants = $items;

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
    public function filterSubMenuUsing(Closure $filter): self
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
            'subActive' => $this->hasActiveDecendants($this->subItems),
            default => parent::__get($name)
        };
    }

    protected function active(): bool
    {
        return is_null($this->activeCheck) ? URL::current() == URL::to($this->url) : (bool) value($this->activeCheck, $this);
    }

    protected function available(): bool
    {
        return collect($this->conditions)->every(fn (bool $condition) => $condition);
    }

    /** @return LazyCollection<int, self> */
    protected function subItems(): LazyCollection
    {
        return LazyCollection::make($this->decendants)
            ->when($this->filter, fn (LazyCollection $items) => $items->filter($this->filter)->each->filterSubMenuUsing($this->filter))
            ->when($this->activeCheck, fn (LazyCollection $items) => $items->each->activeWhen($this->activeCheck));
    }

    /** @param LazyCollection<int, self> $items */
    protected function hasActiveDecendants(LazyCollection $items): bool
    {
        return $items->reduce(fn (bool $active, self $item) => $item->subItems->isEmpty() ? $active : $this->hasActiveDecendants($item->subItems),
            $items->contains->active
        );
    }
}
