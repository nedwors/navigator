<?php

use Nedwors\Navigator\Facades\Nav;
use Nedwors\Navigator\Item;

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
