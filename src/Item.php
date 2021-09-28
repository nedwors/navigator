<?php

namespace Nedwors\LaravelMenu;

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
 */
class Item extends Fluent
{
    use Macroable;

    public string $url = '#0';
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

    public function active(): bool
    {
        return URL::current() == URL::to($this->url);
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

    public function available(): bool
    {
        return collect($this->conditions)->every(fn ($condition) => $condition === true);
    }

    /** @param mixed $name */
    public function __get($name): mixed
    {
        return match (true) {
            $name === 'active' => $this->active(),
            $name === 'available' => $this->available(),
            default => parent::__get($name)
        };
    }
}
