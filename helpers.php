<?php

use Illuminate\Support\Collection;
use Nedwors\Navigator\Facades;
use Nedwors\Navigator\Nav;
use Nedwors\Navigator\Item;

if (!function_exists('navitems')) {
    /** @return Collection<Item> */
    function navitems(string $menu = Nav::DEFAULT): Collection
    {
        return Facades\Nav::items($menu);
    }
}
