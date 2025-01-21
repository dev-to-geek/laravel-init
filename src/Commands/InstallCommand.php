<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    public $signature = 'laravel-init:install {--remove-me : Remove the laravel-init package after installation}
                                              {--all : Install all the packages}';

    public $description = 'Install Pint, PhpStan, Pest, Pail.';

    public function handle(): int
    {

        if ($this->option('all')) {
            // - install pint via composer
            $result = $this->call('laravel-init:install-pint');
            if ($result !== self::SUCCESS) {
                self::fail('❌ Failed to install pint');
            }
        }

        if ($this->option('all')) {
            // - install larastan via composer
            $result = $this->call('laravel-init:install-larastan');
            if ($result !== self::SUCCESS) {
                self::fail('❌ Failed to install larastan');
            }
        }

        if ($this->option('all')) {
            // - install pest via composer using Processees
            $result = $this->call('laravel-init:install-pest');
            if ($result !== self::SUCCESS) {
                self::fail('❌ Failed to install pest');
            }
        }

        if ($this->option('all')) {
            // - install pail via composer using composer
            $result = $this->call('laravel-init:install-pail');
            if ($result !== self::SUCCESS) {
                self::fail('❌ Failed to install pail');
            }
        }

        if ($this->option('all')) {
            // - install rector via composer using composer
            $result = $this->call('laravel-init:install-rector');
            if ($result !== self::SUCCESS) {
                self::fail('❌ Failed to install rector');
            }
        }

        if ($this->option('remove-me')) {
            spin(
                callback: function (): void {
                    $process = Process::run('composer remove dev-to-geek/laravel-init -n');
                    if ($process->failed()) {
                        self::fail('❌ Failed to remove laravel-init');
                    }

                    $process = Process::run('composer install -n');
                    if ($process->failed()) {
                        self::fail('❌ Failed to execute composer install');
                    }

                },
                message: 'Removing laravel-init...'
            );

            $this->info('So long, and thanks for all the fish!');
            $this->info('✅ laravel-init removed successfully');

        }

        // running composer update
        spin(
            callback: function (): void {
                $process = Process::run('composer update -Wn');
                if ($process->failed()) {
                    self::fail('❌ Failed to update composer');
                }
            },
            message: 'Updating composer...'
        );
        $this->info('✅ Composer updated successfully');

        $this->showComposerSnippet();

        return self::SUCCESS;
    }

    public function showComposerSnippet(): void
    {
        $this->info('For your convenience, you can add these lines to composer.json');
        $this->info('"test": "@php artisan test",');
        $this->info('"test-coverage": "@php artisan test --parallel --coverage",');
        $this->info('"analyse": "vendor/bin/phpstan analyse --memory-limit=2G",');
        $this->info('"format": "vendor/bin/pint",');
        $this->info('"refactor": "vendor/bin/rector"');
    }
}
