<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

it('registers the laravel-init:install command', function (): void {
    $commands = Artisan::all();
    expect($commands)->toHaveKey('laravel-init:install');
});
