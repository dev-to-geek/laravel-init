<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallRectorCommand extends Command
{
    public $signature = 'laravel-init:install-rector';

    public $description = 'Install Rector.';

    public function handle(): int
    {

        // - install rector via composer using Processees
        spin(
            callback: function (): void {

                $process = Process::run('composer require rector/rector -n --dev');
                if ($process->failed()) {
                    self::fail('❌ Failed to install rector');
                }

                $result = File::copy(__DIR__.'/../../stubs/rector.php.stub', base_path('rector.php'));
                if (! $result) {
                    self::fail('❌ Failed to copy rector configuration file');
                }
            },
            message: 'Installing rector...'
        );

        $this->info('✅ Rector installed successfully');

        return self::SUCCESS;
    }
}
