<?php

use Illuminate\Support\Facades\Route;
use Nedwors\Navigator\Item;

it("can be instantiated", function () {
    $item = (new Item())
        ->called('Dashboard')
        ->for('dashboard')
        ->heroicon('o-cog');

    expect($item)
        ->name->toBe('Dashboard')
        ->url->toBe('dashboard')
        ->heroicon->toBe('o-cog');
});

it("can have an icon and/or heroicon as they are just strings", function () {
    $item = (new Item())
        ->heroicon('o-cog')
        ->icon('icon.svg');

    expect($item)
        ->heroicon->toBe('o-cog')
        ->icon->toBe('icon.svg');
});

it("has a default route", function () {
    expect((new Item())->url)->toBe('#0');
});

it("will resolve the route for the given named route if it exists", function () {
    $this->withoutExceptionHandling();

    Route::get('/foo/{id}', ['as' => 'foo', fn () => '']);

    $item = (new Item())->for('foo', 1);

    expect($item->url)->toBe(route('foo', 1));
});

it("has composable methods for availability", function (Item $item, bool $available) {
    expect($item->available)->toBe($available);
})->with([
    [fn () => (new Item())->when(true), true],
    [fn () => (new Item())->when(false), false],
    [fn () => (new Item())->when(true)->when(true), true],
    [fn () => (new Item())->when(false)->when(true), false],
    [fn () => (new Item())->unless(false), true],
    [fn () => (new Item())->unless(true), false],
    [fn () => (new Item())->unless(false)->unless(false), true],
    [fn () => (new Item())->unless(true)->unless(false), false],
    [fn () => (new Item())->when(true)->unless(false), true],
    [fn () => (new Item())->when(true)->unless(true), false],
    [fn () => (new Item())->when(false)->unless(false), false],
]);
