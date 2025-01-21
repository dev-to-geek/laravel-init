<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallPailCommand extends Command
{
    public $signature = 'laravel-init:install-pail';

    public $description = 'Install Pail.';

    public function handle(): int
    {

        // - install pail via composer using composer
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

        return self::SUCCESS;
    }
}
