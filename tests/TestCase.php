<?php

namespace Nedwors\LaravelMenu\Tests;

use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    public function foo()
    {
        dd('hey');
    }
}
