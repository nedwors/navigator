<?php

namespace Nedwors\LaravelMenu;

use Illuminate\Support\ServiceProvider;

class LaravelMenuServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        include_once __DIR__ . '../../helpers.php';
    }
}
