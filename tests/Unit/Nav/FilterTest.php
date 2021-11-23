<?php

use Nedwors\Navigator\Facades\Nav;
use Nedwors\Navigator\Item;

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
