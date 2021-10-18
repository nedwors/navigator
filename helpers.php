<?php

use Illuminate\Support\LazyCollection;
use Nedwors\LaravelMenu\Facades;
use Nedwors\LaravelMenu\Menu;
use Nedwors\LaravelMenu\Item;

if (!function_exists('menuitems')) {
    /** @return LazyCollection<Item> */
    function menuitems(string $menu = Menu::DEFAULT): LazyCollection
    {
        return Facades\Menu::items($menu);
    }
}
