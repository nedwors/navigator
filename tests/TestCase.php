<?php

namespace Nedwors\LaravelMenu\Tests;

use Nedwors\LaravelMenu\LaravelMenuServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelMenuServiceProvider::class];
    }
}
