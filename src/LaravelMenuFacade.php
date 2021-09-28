<?php

namespace Nedwors\LaravelMenu;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nedwors\LaravelMenu\Skeleton\SkeletonClass
 */
class LaravelMenuFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-menu';
    }
}
