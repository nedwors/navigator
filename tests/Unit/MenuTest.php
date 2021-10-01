<?php

use Illuminate\Contracts\Foundation\Application;
use Nedwors\LaravelMenu\Facades\Menu;
use Nedwors\LaravelMenu\Item;
use Illuminate\Support\Traits\Macroable;

it("can return a menu item", function () {
    $item = Menu::item('Dashboard');

    expect($item)
        ->toBeInstanceOf(Item::class)
        ->name->toBe('Dashboard');
});

it("can define and retrieve its menu items", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
        Menu::item('Contact Us'),
        Menu::item('Home'),
    ]);

    expect(Menu::items())
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Contact Us'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("can define multiple menus", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
    ], 'app');

    Menu::define(fn () => [
        Menu::item('Manage'),
    ], 'admin');

    expect(Menu::items('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Dashboard');

    expect(Menu::items('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});

it("filters out unavailable items by default", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard')->when(true),
        Menu::item('Contact Us')->when(false),
        Menu::item('Home')->unless(false),
        Menu::item('Settings')->unless(true),
    ]);

    expect(Menu::items())
        ->toHaveCount(2)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("can have its filter defined", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard')->when(true),
        Menu::item('Contact Us')->when(false),
        Menu::item('Home')->unless(false),
        Menu::item('Settings')->unless(true),
    ]);

    Menu::filter(fn (Item $item) => $item->name == 'Dashboard' || $item->name == 'Settings');

    expect(Menu::items())
        ->toHaveCount(2)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Settings'),
        );
});

it("can filter multiple menus", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
        Menu::item('Home')
    ], 'app');

    Menu::define(fn () => [
        Menu::item('Settings'),
        Menu::item('Manage'),
    ], 'admin');

    Menu::filter(fn (Item $item) => $item->name == 'Home', 'app');
    Menu::filter(fn (Item $item) => $item->name == 'Manage', 'admin');

    expect(Menu::items('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Home');

    expect(Menu::items('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});

it("will use the same filter for all menus if the menu is not defined", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
        Menu::item('Home')
    ], 'app');

    Menu::define(fn () => [
        Menu::item('Settings'),
        Menu::item('Manage'),
    ], 'admin');

    Menu::filter(fn (Item $item) => $item->name == 'Home' || $item->name == 'Manage');

    expect(Menu::items('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Home');

    expect(Menu::items('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});

it("receives the application to the closure for its items definition", function () {
    Menu::define(function (Application $app) {
        expect($app)->toBeInstanceOf(Application::class);

        return [Menu::item('Dashboard')];
    });

    Menu::items();
});

it("can define the active check for its items", function () {
    Menu::define(fn () => [
        Menu::item('Settings'),
        Menu::item('Dashboard'),
    ]);

    Menu::activeWhen(fn (Item $item) => $item->name == 'Settings');

    expect(Menu::items()->firstWhere('name', 'Dashboard')->active)->toBeFalse;
    expect(Menu::items()->firstWhere('name', 'Settings')->active)->toBeTrue;
});

it("can define an active check for multiple menus", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
        Menu::item('Home')
    ], 'app');

    Menu::define(fn () => [
        Menu::item('Settings'),
        Menu::item('Manage'),
    ], 'admin');

    Menu::activeWhen(fn (Item $item) => $item->name == 'Home', 'app');
    Menu::activeWhen(fn (Item $item) => $item->name == 'Manage', 'admin');

    expect(Menu::items('app'))->toHaveCount(2);
    expect(Menu::items('app')->filter->active)->toHaveCount(1)->first()->name->toEqual('Home');

    expect(Menu::items('admin'))->toHaveCount(2);
    expect(Menu::items('admin')->filter->active)->toHaveCount(1)->first()->name->toEqual('Manage');
});

it("will use the same active check for all menus if the menu is not defined", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
        Menu::item('Home')
    ], 'app');

    Menu::define(fn () => [
        Menu::item('Settings'),
        Menu::item('Manage'),
    ], 'admin');

    Menu::activeWhen(fn (Item $item) => $item->name == 'Home' || $item->name == 'Manage');

    expect(Menu::items('app'))->toHaveCount(2);
    expect(Menu::items('app')->filter->active)->toHaveCount(1)->first()->name->toEqual('Home');

    expect(Menu::items('admin'))->toHaveCount(2);
    expect(Menu::items('admin')->filter->active)->toHaveCount(1)->first()->name->toEqual('Manage');
});

it("is macroable", function () {
    expect(class_uses(Menu::getFacadeRoot()))->toContain(Macroable::class);
});
