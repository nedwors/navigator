<?php

namespace Nedwors\Navigator\Tests;

use Nedwors\Navigator\NavigatorServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [NavigatorServiceProvider::class];
    }
}
