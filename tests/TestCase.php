<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Tests;

use Dev2Geek\LaravelInit\LaravelInitServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelInitServiceProvider::class,
        ];
    }
}
