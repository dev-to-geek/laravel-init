<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit;

use Dev2Geek\LaravelInit\Commands\InstallCommand;
use Dev2Geek\LaravelInit\Commands\InstallLarastanCommand;
use Dev2Geek\LaravelInit\Commands\InstallPailCommand;
use Dev2Geek\LaravelInit\Commands\InstallPestCommand;
use Dev2Geek\LaravelInit\Commands\InstallPintCommand;
use Dev2Geek\LaravelInit\Commands\InstallRectorCommand;
use Illuminate\Support\ServiceProvider;

class LaravelInitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            InstallCommand::class,
            InstallPestCommand::class,
            InstallPintCommand::class,
            InstallLarastanCommand::class,
            InstallPailCommand::class,
            InstallRectorCommand::class,
        ]);
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-init.php', 'laravel-init');
    }
}
