<?php

namespace Nedwors\Navigator;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Fluent;

/**
 * @property      string           $name     The display name for the item
 * @property      string           $url      The full url for the item
 * @property      ?string          $heroicon The heroicon name for the item
 * @property      ?string          $icon     The icon name/path for the item
 * @property-read bool             $active    Determine if the current item is active
 * @property-read bool             $available Determine if the current item passes its conditions for display
 * @property-read Collection<self> $subItems  Retrieve the item's sub menu items
 * @property-read bool             $hasActiveDecendants Determine if any of the item's decendants are active
 */
class Item extends Fluent
{
    public ?string $url = null;

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
        $translated = __($name);

        $this->name = is_string($translated) ? $translated : $name;

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
    public function subItems(Closure|iterable $items): self
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
    public function filterSubItemsUsing(Closure $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'icon' => $this->icon,
            'heroicon' => $this->heroicon,
            'subItems' => $this->subItems,
        ];
    }

    /** @param mixed $name */
    public function __get($name): mixed
    {
        return match ($name) {
            'active' => $this->active(),
            'available' => $this->available(),
            'subItems' => $this->getSubItems(),
            'hasActiveDecendants' => $this->hasActiveDecendants($this->subItems),
            default => parent::__get($name)
        };
    }

    protected function active(): bool
    {
        return is_null($this->activeCheck) ? URL::current() == URL::to($this->url ?? '') : (bool) value($this->activeCheck, $this);
    }

    protected function available(): bool
    {
        return collect($this->conditions)->every(fn (bool $condition) => $condition);
    }

    /** @return Collection<int, self> */
    protected function getSubItems(): Collection
    {
        return Collection::make($this->decendants)
            ->unless(is_null($this->filter), fn (Collection $items) => $items->filter($this->filter)->each->filterSubItemsUsing($this->filter))
            ->unless(is_null($this->activeCheck), fn (Collection $items) => $items->each->activeWhen($this->activeCheck));
    }

    /** @param Collection<int, self> $items */
    protected function hasActiveDecendants(Collection $items): bool
    {
        return $items->reduce(fn (bool $active, self $item) => $item->subItems->isEmpty() ? $active : $this->hasActiveDecendants($item->subItems),
            $items->contains->active
        );
    }
}
