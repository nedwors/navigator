<?php

use Illuminate\Support\LazyCollection;
use Nedwors\Navigator\Facades;
use Nedwors\Navigator\Nav;
use Nedwors\Navigator\Item;

if (!function_exists('navitems')) {
    /** @return LazyCollection<Item> */
    function navitems(string $menu = Nav::DEFAULT): LazyCollection
    {
        return Facades\Nav::items($menu);
    }
}
