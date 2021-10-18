<?php

use Nedwors\LaravelMenu\Facades\Menu;

it("can define and retrieve its menu items", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
        Menu::item('Contact Us'),
        Menu::item('Home'),
    ]);

    expect(menuitems())
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Contact Us'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("can define and retrieve different menus", function () {
    Menu::define(fn () => [
        Menu::item('Dashboard'),
        Menu::item('Contact Us'),
        Menu::item('Home'),
    ]);

    expect(menuitems())
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

    expect(menuitems('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Dashboard');

    expect(menuitems('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});
