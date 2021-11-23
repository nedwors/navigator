<?php

use Nedwors\Navigator\Facades\Nav;
use Nedwors\Navigator\Item;
use Illuminate\Support\Traits\Macroable;
use Nedwors\Navigator\Tests\Doubles\OtherItem;

it("can return a nav item", function () {
    $item = Nav::item('Dashboard');

    expect($item)
        ->toBeInstanceOf(Item::class)
        ->name->toBe('Dashboard');
});

it("can return a custom nav item", function () {
    $item = Nav::using(OtherItem::class)->item('Dashboard');

    expect($item)
        ->toBeInstanceOf(OtherItem::class)
        ->name->toBe('Dashboard');
});

it("is macroable", function () {
    expect(class_uses(Nav::getFacadeRoot()))->toContain(Macroable::class);
});
