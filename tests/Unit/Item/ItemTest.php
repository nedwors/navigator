<?php

use Illuminate\Support\Facades\Route;
use Nedwors\Navigator\Item;

use function Pest\version;

it('can be instantiated', function () {
    $item = (new Item)
        ->called('Dashboard')
        ->for('dashboard')
        ->heroicon('o-cog');

    expect($item)
        ->name->toBe('Dashboard')
        ->url->toBe('dashboard')
        ->heroicon->toBe('o-cog');
});

it('can be transformed into an array', function () {
    $item = (new Item)
        ->called('Dashboard')
        ->for('dashboard')
        ->heroicon('o-cog')
        ->foo('bar');

    expect($item->toArray())->toEqual([
        'name' => 'Dashboard',
        'url' => 'dashboard',
        'icon' => null,
        'heroicon' => 'o-cog',
        'subItems' => collect(),
        'active' => false,
        'hasActiveDescendants' => false,
        'attributes' => ['foo' => 'bar'],
    ]);
});

it('can be transformed into json', function () {
    $item = (new Item)
        ->called('Dashboard')
        ->for('dashboard')
        ->heroicon('o-cog');

    expect($item->toJson())->toEqual(json_encode([
        'name' => 'Dashboard',
        'url' => 'dashboard',
        'icon' => null,
        'heroicon' => 'o-cog',
        'subItems' => collect(),
        'active' => false,
        'hasActiveDescendants' => false,
        'attributes' => [],
    ]));
});

it('can have an icon and/or heroicon as they are just strings', function () {
    $item = (new Item)
        ->heroicon('o-cog')
        ->icon('icon.svg');

    expect($item)
        ->heroicon->toBe('o-cog')
        ->icon->toBe('icon.svg');
});

it('has a default null route', function () {
    expect((new Item)->url)->toBeNull;
});

it('will resolve the route for the given named route if it exists', function () {
    $this->withoutExceptionHandling();

    Route::get('/foo/{id}', ['as' => 'foo', fn () => '']);

    $item = (new Item)->for('foo', 1);

    expect($item->url)->toBe(route('foo', 1));
});

if (version_compare(version(), '2') > -1) {
    it('has composable methods for availability', function (Item $item, bool $available) {
        expect($item->available)->toBe($available);
    })->with([
        fn () => [(new Item)->includeWhen(true), true],
        fn () => [(new Item)->includeWhen(false), false],
        fn () => [(new Item)->includeWhen(true)->includeWhen(true), true],
        fn () => [(new Item)->includeWhen(false)->includeWhen(true), false],
        fn () => [(new Item)->includeUnless(false), true],
        fn () => [(new Item)->includeUnless(true), false],
        fn () => [(new Item)->includeUnless(false)->includeUnless(false), true],
        fn () => [(new Item)->includeUnless(true)->includeUnless(false), false],
        fn () => [(new Item)->includeWhen(true)->includeUnless(false), true],
        fn () => [(new Item)->includeWhen(true)->includeUnless(true), false],
        fn () => [(new Item)->includeWhen(false)->includeUnless(false), false],
    ]);
}
