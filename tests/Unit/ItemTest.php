<?php

use Illuminate\Support\LazyCollection;
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

it("can have a sub menu", function () {
    $item = (new Item())
        ->subMenu([
            (new Item())->called('Dashboard')->for('/dashboard'),
            (new Item())->called('Settings')->for('/settings'),
        ]);

    $subItems = $item->subItems;

    expect($subItems)->toHaveCount(2)->toBeInstanceOf(LazyCollection::class);
    expect($subItems->firstWhere('name', 'Dashboard')->url)->toEqual('/dashboard');
    expect($subItems->firstWhere('name', 'Settings')->url)->toEqual('/settings');
});

it("can have a sub menu as a generator", function () {
    $item = (new Item())
        ->subMenu(fn () => yield from [
            (new Item())->called('Dashboard')->for('/dashboard'),
            (new Item())->called('Settings')->for('/settings')
        ]);

    $subItems = $item->subItems;

    expect($subItems)->toHaveCount(2)->toBeInstanceOf(LazyCollection::class);
    expect($subItems->firstWhere('name', 'Dashboard')->url)->toEqual('/dashboard');
    expect($subItems->firstWhere('name', 'Settings')->url)->toEqual('/settings');
});

it("can theoretically have countably infinite sub menus...", function () {
    $item = (new Item())->subMenu([
        (new Item())->called('Foo')->subMenu(fn () =>  yield from [
            (new Item())->called('Bar')->subMenu(fn () =>
                yield (new Item())->called('Whizz')
            )
        ])
    ]);

    expect($item->subItems->first()->subItems->first()->subItems->first()->name)->toEqual('Whizz');
});

it("can determine if any of its decendants are active", function () {
    $this->withoutExceptionHandling();

    $nope = (new Item())->for('#0')->subMenu([
        $bar = (new Item())->for('bar'),
        $foo = (new Item())->for('foo'),
    ]);

    Route::get('/foo', ['as' => 'foo', function () use (&$nope, &$bar, &$foo, &$whizz) {
        expect($nope->active)->toBeFalse;
        expect($nope->subActive)->toBeTrue;

        expect($bar->active)->toBeFalse;
        expect($bar->subActive)->toBeFalse;

        expect($foo->active)->toBeTrue;
        expect($foo->subActive)->toBeFalse;
    }]);

    $this->get(route('foo'));
});

it("can determine if any of its nested decendants are active", function () {
    $this->withoutExceptionHandling();

    $nope = (new Item())->for('#0')->subMenu([
        $nopeAgain = (new Item())->for('#1')->subMenu([
            $bar = (new Item())->for('bar')
        ]),
        $stillNope = (new Item())->for('#0')->subMenu([
            $andAgain = (new Item())->for('#2')->subMenu([
                $whizz = (new Item())->for('whizz'),
                $foo = (new Item())->for('foo')
            ])
        ])
    ]);

    Route::get('/foo', ['as' => 'foo', function () use (&$nope, &$nopeAgain, &$stillNope, &$foo) {
        expect($nope->active)->toBeFalse;
        expect($nope->subActive)->toBeTrue;

        expect($nopeAgain->active)->toBeFalse;
        expect($nopeAgain->subActive)->toBeFalse;

        expect($stillNope->active)->toBeFalse;
        expect($stillNope->subActive)->toBeTrue;

        expect($foo->active)->toBeTrue;
        expect($foo->subActive)->toBeFalse;
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
