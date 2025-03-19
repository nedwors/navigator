<?php

use Illuminate\Support\Collection;
use Nedwors\Navigator\Facades;
use Nedwors\Navigator\Item;
use Nedwors\Navigator\Nav;

if (! function_exists('navitems')) {
    /** @return Collection<Item> */
    function navitems(string $menu = Nav::DEFAULT): Collection
    {
        return Facades\Nav::items($menu);
    }
}
