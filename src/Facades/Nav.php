<?php

namespace Nedwors\Navigator\Facades;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Nedwors\Navigator;
use Nedwors\Navigator\Item;

/**
 * @method static Item item(string $name)                                     Create a new menu item
 * @method static self define(Closure $items, ?string $menu = null)           Define a menu of items
 * @method static Collection<Item> items(?string $menu = null)                Retrieve items in the given menu
 * @method static string toJson(?string $menu = null, mixed $options = 0)     Retrieve items in the given menu as json, using Laravel Collection's toJson method
 * @method static self filter(Closure $filter, ?string $menu = null)          Define how the items should be filtered upon retrieval
 * @method static self activeWhen(Closure $activeCheck, ?string $menu = null) Define what qualifies an item as active
 */
class Nav extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Navigator\Nav::class;
    }
}
