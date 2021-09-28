<?php

use Illuminate\Support\Facades\Route;
use Nedwors\LaravelMenu\Item;

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
    Route::get('/foo/{id}', ['as' => 'foo', fn () => '']);

    $item = (new Item())->for('foo', 1);

    expect($item->url)->toBe(route('foo', 1));
});

it("can determine if the current item is active", function () {
    $this->withoutExceptionHandling();
    
    $foo = (new Item())->for('foo');
    $nope = (new Item())->for('#0');

    Route::get('/foo', ['as' => 'foo', function () use (&$foo, &$nope) {
        expect($foo->active)->toBeTrue;
        expect($nope->active)->toBeFalse;
    }]);

    $this->get(route('foo'));
});
