<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use Nedwors\Navigator\Item;

it("can have sub items", function () {
    $item = (new Item())
        ->subItems([
            (new Item())->called('Dashboard')->for('/dashboard'),
            (new Item())->called('Settings')->for('/settings'),
        ]);

    $subItems = $item->subItems;

    expect($subItems)->toHaveCount(2)->toBeInstanceOf(Collection::class);
    expect($subItems->firstWhere('name', 'Dashboard')->url)->toEqual('/dashboard');
    expect($subItems->firstWhere('name', 'Settings')->url)->toEqual('/settings');
});

it("can theoretically have countably infinite sub items...", function () {
    $item = (new Item())->subItems([
        (new Item())->called('Foo')->subItems([
            (new Item())->called('Bar')->subItems([
                (new Item())->called('Whizz')
            ])
        ])
    ]);

    expect($item->subItems->first()->subItems->first()->subItems->first()->name)->toEqual('Whizz');
});

it("can determine if any of its decendants are active", function () {
    $this->withoutExceptionHandling();

    $nope = (new Item())->for('#0')->subItems([
        $bar = (new Item())->for('bar'),
        $foo = (new Item())->for('foo'),
    ]);

    Route::get('/foo', ['as' => 'foo', function () use (&$nope, &$bar, &$foo, &$whizz) {
        expect($nope->active)->toBeFalse;
        expect($nope->hasActiveDecendants)->toBeTrue;

        expect($bar->active)->toBeFalse;
        expect($bar->hasActiveDecendants)->toBeFalse;

        expect($foo->active)->toBeTrue;
        expect($foo->hasActiveDecendants)->toBeFalse;
    }]);

    $this->get(route('foo'));
});

it("can determine if any of its nested decendants are active", function () {
    $this->withoutExceptionHandling();

    $nope = (new Item())->for('#0')->subItems([
        $nopeAgain = (new Item())->for('#1')->subItems([
            $bar = (new Item())->for('bar')
        ]),
        $stillNope = (new Item())->for('#0')->subItems([
            $andAgain = (new Item())->for('#2')->subItems([
                $whizz = (new Item())->for('whizz'),
                $foo = (new Item())->for('foo')
            ])
        ])
    ]);

    Route::get('/foo', ['as' => 'foo', function () use (&$nope, &$nopeAgain, &$stillNope, &$foo) {
        expect($nope->active)->toBeFalse;
        expect($nope->hasActiveDecendants)->toBeTrue;

        expect($nopeAgain->active)->toBeFalse;
        expect($nopeAgain->hasActiveDecendants)->toBeFalse;

        expect($stillNope->active)->toBeFalse;
        expect($stillNope->hasActiveDecendants)->toBeTrue;

        expect($foo->active)->toBeTrue;
        expect($foo->hasActiveDecendants)->toBeFalse;
    }]);

    $this->get(route('foo'));
});
