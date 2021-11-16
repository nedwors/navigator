<?php

namespace Nedwors\Navigator;

use Illuminate\Support\ServiceProvider;

class NavigatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        include_once __DIR__ . '../../helpers.php';
    }
}
