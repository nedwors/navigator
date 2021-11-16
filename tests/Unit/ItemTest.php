<?php

use Illuminate\Support\LazyCollection;
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

it("can determine if the current item is active with a url", function () {
    $this->withoutExceptionHandling();

    $foo = (new Item())->for('/foo');
    $nope = (new Item())->for('/bar');

    Route::get('/foo', function () use (&$foo, &$nope) {
        expect($foo->active)->toBeTrue;
        expect($nope->active)->toBeFalse;
    });

    $this->get('/foo');
});

it("can have a custom callback to determine its active state", function () {
    $this->withoutExceptionHandling();

    $foo = (new Item())->for('foo')->activeWhen(fn ($item) => false);
    $nope = (new Item())->for('#0')->activeWhen(fn ($item) => url()->current() == url()->to(route('foo')));

    Route::get('/foo', ['as' => 'foo', function () use (&$foo, &$nope) {
        expect($foo->active)->toBeFalse;
        expect($nope->active)->toBeTrue;
    }]);

    $this->get(route('foo'));
});

it("can have sub items", function () {
    $item = (new Item())
        ->subItems([
            (new Item())->called('Dashboard')->for('/dashboard'),
            (new Item())->called('Settings')->for('/settings'),
        ]);

    $subItems = $item->subItems;

    expect($subItems)->toHaveCount(2)->toBeInstanceOf(LazyCollection::class);
    expect($subItems->firstWhere('name', 'Dashboard')->url)->toEqual('/dashboard');
    expect($subItems->firstWhere('name', 'Settings')->url)->toEqual('/settings');
});

it("can have sub items as a generator", function () {
    $item = (new Item())
        ->subItems(fn () => yield from [
            (new Item())->called('Dashboard')->for('/dashboard'),
            (new Item())->called('Settings')->for('/settings')
        ]);

    $subItems = $item->subItems;

    expect($subItems)->toHaveCount(2)->toBeInstanceOf(LazyCollection::class);
    expect($subItems->firstWhere('name', 'Dashboard')->url)->toEqual('/dashboard');
    expect($subItems->firstWhere('name', 'Settings')->url)->toEqual('/settings');
});

it("can theoretically have countably infinite sub items...", function () {
    $item = (new Item())->subItems([
        (new Item())->called('Foo')->subItems(fn () =>  yield from [
            (new Item())->called('Bar')->subItems(fn () =>
                yield (new Item())->called('Whizz')
            )
        ])
    ]);

    expect($item->subItems->first()->subItems->first()->subItems->first()->name)->toEqual('Whizz');
});

it("can determine if any of its decendants are active", function () {
    $this->withoutExceptionHandling();

    $nope = (new Item())->for('#0')->subItems([
        $bar = (new Item())->for('bar'),
        $foo = (new Item())->for('foo'),
    ]);

    Route::get('/foo', ['as' => 'foo', function () use (&$nope, &$bar, &$foo, &$whizz) {
        expect($nope->active)->toBeFalse;
        expect($nope->hasActiveDecendants)->toBeTrue;

        expect($bar->active)->toBeFalse;
        expect($bar->hasActiveDecendants)->toBeFalse;

        expect($foo->active)->toBeTrue;
        expect($foo->hasActiveDecendants)->toBeFalse;
    }]);

    $this->get(route('foo'));
});

it("can determine if any of its nested decendants are active", function () {
    $this->withoutExceptionHandling();

    $nope = (new Item())->for('#0')->subItems([
        $nopeAgain = (new Item())->for('#1')->subItems([
            $bar = (new Item())->for('bar')
        ]),
        $stillNope = (new Item())->for('#0')->subItems([
            $andAgain = (new Item())->for('#2')->subItems([
                $whizz = (new Item())->for('whizz'),
                $foo = (new Item())->for('foo')
            ])
        ])
    ]);

    Route::get('/foo', ['as' => 'foo', function () use (&$nope, &$nopeAgain, &$stillNope, &$foo) {
        expect($nope->active)->toBeFalse;
        expect($nope->hasActiveDecendants)->toBeTrue;

        expect($nopeAgain->active)->toBeFalse;
        expect($nopeAgain->hasActiveDecendants)->toBeFalse;

        expect($stillNope->active)->toBeFalse;
        expect($stillNope->hasActiveDecendants)->toBeTrue;

        expect($foo->active)->toBeTrue;
        expect($foo->hasActiveDecendants)->toBeFalse;
    }]);

    $this->get(route('foo'));
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
