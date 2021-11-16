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

it("can define and retrieve its nav items", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Contact Us'),
        Nav::item('Home'),
    ]);

    expect(Nav::items())
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Contact Us'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("will return an empty collection for an undefined nav", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Contact Us'),
        Nav::item('Home'),
    ]);

    expect(Nav::items('foo-bar'))->toBeEmpty;
});

it("can define nav items as a generator", function () {
    Nav::define(fn () => yield from [
        Nav::item('Dashboard'),
        Nav::item('Contact Us'),
        Nav::item('Home'),
    ]);

    expect(Nav::items())
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Contact Us'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("can define multiple navs", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
    ], 'app');

    Nav::define(fn () => [
        Nav::item('Manage'),
    ], 'admin');

    expect(Nav::items('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Dashboard');

    expect(Nav::items('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});

it("filters out unavailable items by default", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard')->when(true),
        Nav::item('Contact Us')->when(false),
        Nav::item('Home')->unless(false),
        Nav::item('Settings')->unless(true),
    ]);

    expect(Nav::items())
        ->toHaveCount(2)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("can filter sub navs", function () {
    Nav::define(fn () => [
        Nav::item('Foo')->subItems([
            Nav::item('Foo Child')->when(false)
        ]),
        Nav::item('Bar')->subItems([
            Nav::item('Bar Child')->when(false),
            Nav::item('Bar Child 2')->when(true),
        ]),
    ]);

    $items = Nav::items();

    expect($items)
        ->toHaveCount(2);

    expect($items->firstWhere('name', 'Foo')->subItems)
        ->toHaveCount(0);

    expect($items->firstWhere('name', 'Bar')->subItems)
        ->toHaveCount(1)
        ->first()->name->toEqual('Bar Child 2');
});

it("can have its filter defined", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard')->when(true),
        Nav::item('Contact Us')->when(false),
        Nav::item('Home')->unless(false),
        Nav::item('Settings')->unless(true),
    ]);

    Nav::filter(fn (Item $item) => $item->name == 'Dashboard' || $item->name == 'Settings');

    expect(Nav::items())
        ->toHaveCount(2)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Settings'),
        );
});

it("will use a defined filter for sub navs", function () {
    Nav::define(fn () => [
        Nav::item('Foo')->subItems([
            Nav::item('Foo')
        ]),
        Nav::item('Bar')->subItems([
            Nav::item('Bar')->subItems([
                Nav::item('Foo'),
                Nav::item('Whizz'),
            ])
        ]),
    ]);

    Nav::filter(fn (Item $item) => $item->name == 'Foo' || $item->name == 'Bar');

    $items = Nav::items();

    expect($items)
        ->toHaveCount(2);

    expect($items->firstWhere('name', 'Foo')->subItems)
        ->toHaveCount(1);

    expect($items->firstWhere('name', 'Bar')->subItems)
        ->toHaveCount(1)
        ->first()->name->toEqual('Bar');

    expect($items->firstWhere('name', 'Bar')->subItems->firstWhere('name', 'Bar')->subItems)
        ->toHaveCount(1)
        ->first()->name->toEqual('Foo');
});

it("can filter multiple navs", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Home')->subItems([
            Nav::item('Home')->subItems([
                Nav::item('Foo'),
                Nav::item('Settings')
            ])
        ])
    ], 'app');

    Nav::define(fn () => [
        Nav::item('Settings'),
        Nav::item('Manage'),
    ], 'admin');

    Nav::filter(fn (Item $item) => $item->name == 'Home' || $item->name == 'Foo', 'app');
    Nav::filter(fn (Item $item) => $item->name == 'Manage', 'admin');

    expect(Nav::items('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Home');

    expect(Nav::items('app')->first()->subItems->first()->subItems)
        ->toHaveCount(1)
        ->first()->name->toEqual('Foo');

    expect(Nav::items('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});

it("will use the same filter for all navs if the nav is not defined", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Home')
    ], 'app');

    Nav::define(fn () => [
        Nav::item('Settings'),
        Nav::item('Manage'),
    ], 'admin');

    Nav::filter(fn (Item $item) => $item->name == 'Home' || $item->name == 'Manage');

    expect(Nav::items('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Home');

    expect(Nav::items('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});

it("can define the active check for its items", function () {
    Nav::define(fn () => [
        Nav::item('Settings'),
        Nav::item('Dashboard')->subItems([
            Nav::item('Home')->subItems([
                Nav::item('Settings')
            ])
        ]),
    ]);

    Nav::activeWhen(fn (Item $item) => $item->name == 'Settings');

    expect(Nav::items()->firstWhere('name', 'Settings')->active)->toBeTrue;
    expect(Nav::items()->firstWhere('name', 'Dashboard')->active)->toBeFalse;
    expect(Nav::items()->firstWhere('name', 'Dashboard')->hasActiveDecendants)->toBeTrue;

    $nested = Nav::items()->firstWhere('name', 'Dashboard')->subItems->first()->subItems->first();

    expect($nested->name)->toEqual('Settings');
    expect($nested->active)->toBeTrue;
});

it("can define an active check for multiple navs", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Home')
    ], 'app');

    Nav::define(fn () => [
        Nav::item('Settings'),
        Nav::item('Manage'),
    ], 'admin');

    Nav::activeWhen(fn (Item $item) => $item->name == 'Home', 'app');
    Nav::activeWhen(fn (Item $item) => $item->name == 'Manage', 'admin');

    expect(Nav::items('app'))->toHaveCount(2);
    expect(Nav::items('app')->filter->active)->toHaveCount(1)->first()->name->toEqual('Home');

    expect(Nav::items('admin'))->toHaveCount(2);
    expect(Nav::items('admin')->filter->active)->toHaveCount(1)->first()->name->toEqual('Manage');
});

it("will use the same active check for all navs if the nav is not defined", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Home')
    ], 'app');

    Nav::define(fn () => [
        Nav::item('Settings'),
        Nav::item('Manage'),
    ], 'admin');

    Nav::activeWhen(fn (Item $item) => $item->name == 'Home' || $item->name == 'Manage');

    expect(Nav::items('app'))->toHaveCount(2);
    expect(Nav::items('app')->filter->active)->toHaveCount(1)->first()->name->toEqual('Home');

    expect(Nav::items('admin'))->toHaveCount(2);
    expect(Nav::items('admin')->filter->active)->toHaveCount(1)->first()->name->toEqual('Manage');
});

it("is macroable", function () {
    expect(class_uses(Nav::getFacadeRoot()))->toContain(Macroable::class);
});
