<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    public $signature = 'laravel-init:install {--remove-me : Remove the laravel-init package after installation}';

    public $description = 'Install Pint, PhpStan, Pest, Pail.';

    public function handle(): int
    {

        /**
         * install pint via composer
         */
        spin(
            callback: function (): void {
                $pintProcess = Process::run('composer require laravel/pint --dev -n');

                if ($pintProcess->failed()) {
                    self::fail('❌ Failed to install pint');
                }

                $result = File::copy(__DIR__.'/../../stubs/pint.json.stub', base_path('pint.json'));
                if (! $result) {
                    self::fail('❌ Failed to copy pint configuration file');
                }
            },
            message: 'Installing pint...');

        $this->info('✅ Pint installed successfully');

        // - install larastan via composer
        spin(
            callback: function (): void {
                $process = Process::run('composer require --dev "larastan/larastan:^3.0" -n');

                if ($process->failed()) {
                    self::fail('❌ Failed to install larastan');
                }

                $result = File::copy(__DIR__.'/../../stubs/phpstan.neon.dist.stub', base_path('phpstan.neon.dist'));
                if (! $result) {
                    self::fail('❌ Failed to copy phpstan configuration file');
                }
            },
            message: 'Installing larastan...');

        $this->info('✅ Larastan installed successfully');

        // - install pest via composer using Processees
        spin(
            callback: function (): void {
                $process = Process::run('composer remove phpunit/phpunit -n');

                $process = Process::run('composer require pestphp/pest --dev --with-all-dependencies -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install pestphp');
                }

                $process = Process::run('./vendor/bin/pest --init');
                if ($process->failed()) {
                    self::fail('❌ Failed to init pest');
                }

                $process = Process::run('composer require mockery/mockery --dev -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install mockery');
                }

                $process = Process::run('composer require pestphp/pest-plugin-faker --dev -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install pest plugin faker');
                }

                $process = Process::run('composer require pestphp/pest-plugin-laravel --dev -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install pest plugin laravel');
                }

                $process = Process::run('composer require pestphp/pest-plugin-livewire --dev -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install pest plugin livewire');
                }

            },
            message: 'Installing pestphp and plugins...');

        $this->info('✅ pestphp installed successfully');

        // - install pail via composer using Processees
        spin(
            callback: function (): void {

                $process = Process::run('composer require laravel/pail -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install pail');
                }
            },
            message: 'Installing pail...'
        );

        $this->info('✅ Pail installed successfully');

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

        return self::SUCCESS;
    }
}
