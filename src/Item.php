<?php

namespace Nedwors\LaravelMenu;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/**
 * @property-read bool $active
 */
class Item
{
    public string $name;
    public string $url = '#0';
    public string $icon;
    public string $heroicon;

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

    /** @param mixed $name */
    public function __get($name): mixed
    {
        return $this->$name();
    }
}
