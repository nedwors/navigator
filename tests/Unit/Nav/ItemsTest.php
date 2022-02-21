<?php

use Nedwors\Navigator\Facades\Nav;

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
