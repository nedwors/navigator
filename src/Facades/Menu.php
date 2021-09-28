<?php

namespace Nedwors\LaravelMenu\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Nedwors\LaravelMenu;
use Nedwors\LaravelMenu\Item;

/**
 * @method static Item item(string $name)
 * @method static Collection<Item> items()
 * @method static self define(Closure $items)
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelMenu\Menu::class;
    }
}
