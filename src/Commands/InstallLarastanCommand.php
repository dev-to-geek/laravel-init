<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallLarastanCommand extends Command
{
    public $signature = 'laravel-init:install-larastan';

    public $description = 'Install Larastan and Phpstan';

    public function handle(): int
    {

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

        return self::SUCCESS;
    }
}
