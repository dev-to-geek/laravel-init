<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallPestCommand extends Command
{
    public $signature = 'laravel-init:install-pest';

    public $description = 'Install Pest';

    public function handle(): int
    {

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

        return self::SUCCESS;
    }
}
