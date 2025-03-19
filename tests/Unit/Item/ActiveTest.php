<?php

use Illuminate\Support\Facades\Route;
use Nedwors\Navigator\Item;

it('can determine if the current item is active', function () {
    $this->withoutExceptionHandling();

    $foo = (new Item)->for('foo');
    $nope = (new Item)->for('#0');

    Route::get('/foo', ['as' => 'foo', function () use (&$foo, &$nope) {
        expect($foo->active)->toBeTrue;
        expect($nope->active)->toBeFalse;
    }]);

    $this->get(route('foo'));
});

it('can determine if the current item is active with a url', function () {
    $this->withoutExceptionHandling();

    $foo = (new Item)->for('/foo');
    $nope = (new Item)->for('/bar');

    Route::get('/foo', function () use (&$foo, &$nope) {
        expect($foo->active)->toBeTrue;
        expect($nope->active)->toBeFalse;
    });

    $this->get('/foo');
});

it('can have a custom callback to determine its active state', function () {
    $this->withoutExceptionHandling();

    $foo = (new Item)->for('foo')->activeWhen(fn ($item) => false);
    $nope = (new Item)->for('#0')->activeWhen(fn ($item) => url()->current() == url()->to(route('foo')));

    Route::get('/foo', ['as' => 'foo', function () use (&$foo, &$nope) {
        expect($foo->active)->toBeFalse;
        expect($nope->active)->toBeTrue;
    }]);

    $this->get(route('foo'));
});
