<?php

use Nedwors\Navigator\Facades\Nav;
use Nedwors\Navigator\Item;
use Illuminate\Support\Traits\Macroable;

it("can return a nav item", function () {
    $item = Nav::item('Dashboard');

    expect($item)
        ->toBeInstanceOf(Item::class)
        ->name->toBe('Dashboard');
});

it("is macroable", function () {
    expect(class_uses(Nav::getFacadeRoot()))->toContain(Macroable::class);
});
