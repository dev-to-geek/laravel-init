<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallPintCommand extends Command
{
    public $signature = 'laravel-init:install-pint';

    public $description = 'Install Pint';

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

        return self::SUCCESS;
    }
}
