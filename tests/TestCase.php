<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Tests;

use Dev2Geek\LaravelInit\LaravelInitServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'Dev2Geek\\LaravelInit\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-init_table.php.stub';
        $migration->up();
        */
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelInitServiceProvider::class,
        ];
    }
}
