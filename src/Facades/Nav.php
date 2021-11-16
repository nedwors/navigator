<?php

namespace Nedwors\Navigator\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\LazyCollection;
use Nedwors\Navigator;
use Nedwors\Navigator\Item;

/**
 * @method static self using(string $class)                                   Define which menu item class should be resolved for each item
 * @method static Item item(string $name)                                     Create a new menu item
 * @method static self define(Closure $items, ?string $menu = null)           Define a menu of items
 * @method static LazyCollection<Item> items(?string $menu = null)            Retrieve items in the given menu
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
