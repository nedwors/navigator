<?php

use Nedwors\Navigator\Facades\Nav;

it("can define and retrieve its nav items", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Contact Us'),
        Nav::item('Home'),
    ]);

    expect(navitems())
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Contact Us'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("can define and retrieve different menus", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Contact Us'),
        Nav::item('Home'),
    ]);

    expect(navitems())
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Contact Us'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("can define multiple menus", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
    ], 'app');

    Nav::define(fn () => [
        Nav::item('Manage'),
    ], 'admin');

    expect(navitems('app'))
        ->toHaveCount(1)
        ->first()->name->toBe('Dashboard');

    expect(navitems('admin'))
        ->toHaveCount(1)
        ->first()->name->toBe('Manage');
});
