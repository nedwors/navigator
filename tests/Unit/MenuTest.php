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

it("receives the application to the closure for its items definition", function () {
    Menu::define(function (Application $app) {
        expect($app)->toBeInstanceOf(Application::class);

        return [Menu::item('Dashboard')];
    });

    Menu::items();
});

it("is macroable", function () {
    expect(class_uses(Menu::getFacadeRoot()))->toContain(Macroable::class);
});
