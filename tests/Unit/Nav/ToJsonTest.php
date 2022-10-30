<?php

use Nedwors\Navigator\Facades\Nav;

it("can retrieve its nav items as json", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Contact Us'),
        Nav::item('Home'),
    ]);

    expect(Nav::toJson())
        ->json()
        ->toHaveCount(3)
        ->sequence(
            fn ($item) => $item->name->toBe('Dashboard'),
            fn ($item) => $item->name->toBe('Contact Us'),
            fn ($item) => $item->name->toBe('Home'),
        );
});

it("encodes the appropriate information to json", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard')
            ->for('/foo')
            ->icon('foo.svg')
            ->heroicon('o-cog')
            ->subItems([
                Nav::item('Users')
                    ->for('/users')
                    ->icon('user.svg')
                    ->heroicon('o-user')
            ])
    ]);

    $array = [
        [
            'name' => 'Dashboard',
            'url' => '/foo',
            'icon' => 'foo.svg',
            'heroicon' => 'o-cog',
            'subItems' => [
                [
                    'name' => 'Users',
                    'url' => '/users',
                    'icon' => 'user.svg',
                    'heroicon' => 'o-user',
                    'subItems' => [],
                    'active' => false
                ]
                ],
                'active' => false
        ]
    ];

    expect(Nav::toJson())->toEqual(json_encode($array));
});

it("will return an empty json object for an undefined nav", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
        Nav::item('Contact Us'),
        Nav::item('Home'),
    ]);

    expect(Nav::toJson('foo-bar'))
        ->json()
        ->toBeEmpty;
});

it("can define and retreive multiple navs as json", function () {
    Nav::define(fn () => [
        Nav::item('Dashboard'),
    ], 'app');

    Nav::define(fn () => [
        Nav::item('Manage'),
    ], 'admin');

    expect(Nav::toJson('app'))
        ->json()
        ->toHaveCount(1)
        ->{0}->name->toBe('Dashboard');

    expect(Nav::toJson('admin'))
        ->json()
        ->toHaveCount(1)
        ->{0}->name->toBe('Manage');
});
