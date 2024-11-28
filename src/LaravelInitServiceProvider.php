<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit;

use Dev2Geek\LaravelInit\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;

class LaravelInitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            InstallCommand::class,
        ]);
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-init.php', 'laravel-init');
    }
}
