<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    public $signature = 'laravel-init:install';

    public $description = 'Install Pint, PhpStan, Pest, Pail.';

    /**
     * Execute a composer command and fail if it returns an error.
     */
    protected function runComposerCommand(string $command, string $errorMessage): void
    {
        $process = Process::run($command);

        if ($process->failed()) {
            $this->fail($errorMessage);
        }
    }

    /**
     * Copy a stub configuration file to the project root.
     */
    protected function copyStubFile(string $stubFileName, string $destinationFileName): void
    {
        $source = __DIR__."/../../stubs/{$stubFileName}";
        $destination = base_path($destinationFileName);

        $result = File::copy($source, $destination);

        if (! $result) {
            $this->fail("❌ Failed to copy {$destinationFileName} configuration file");
        }
    }

    /**
     * Install a package with a spinner and success message.
     */
    protected function installPackageWithSpinner(string $packageName, callable $callback): void
    {
        spin(
            callback: $callback,
            message: "Installing {$packageName}..."
        );

        $this->info("✅ {$packageName} installed successfully");
    }

    public function handle(): int
    {

        $this->installPackageWithSpinner('Pint', function (): void {
            $this->runComposerCommand('composer require laravel/pint --dev -n', '❌ Failed to install pint');
            $this->copyStubFile('pint.json.stub', 'pint.json');
        });

        $this->installPackageWithSpinner('Larastan', function (): void {
            $this->runComposerCommand('composer require --dev "larastan/larastan:^3.1" -n', '❌ Failed to install larastan');
            $this->copyStubFile('phpstan.neon.dist.stub', 'phpstan.neon.dist');
        });

        $this->installPackageWithSpinner('pestphp and plugins', function (): void {
            Process::run('composer remove phpunit/phpunit -n');
            $this->runComposerCommand('composer require pestphp/pest --dev --with-all-dependencies -n', '❌ Failed to install pestphp');
            $this->runComposerCommand('./vendor/bin/pest --init', '❌ Failed to init pest');
            $this->runComposerCommand('composer require mockery/mockery --dev -n', '❌ Failed to install mockery');
            $this->runComposerCommand('composer require pestphp/pest-plugin-faker --dev -n', '❌ Failed to install pest plugin faker');
            $this->runComposerCommand('composer require pestphp/pest-plugin-laravel --dev -n', '❌ Failed to install pest plugin laravel');
            $this->runComposerCommand('composer require pestphp/pest-plugin-livewire --dev -n', '❌ Failed to install pest plugin livewire');
        });

        $this->installPackageWithSpinner('Pail', function (): void {
            $this->runComposerCommand('composer require laravel/pail -n', '❌ Failed to install pail');
        });

        $this->installPackageWithSpinner('Rector', function (): void {
            $this->runComposerCommand('composer require rector/rector -n --dev', '❌ Failed to install rector');
            $this->copyStubFile('rector.php.stub', 'rector.php');
        });

        $this->installPackageWithSpinner('Laravel Boost', function (): void {
            $this->runComposerCommand('composer require laravel/boost --dev -n', '❌ Failed to install laravel boost');
        });

        $this->installPackageWithSpinner('Composer', function (): void {
            $this->runComposerCommand('composer update -Wn', '❌ Failed to update composer');
        });

        $this->info('For your convenience, you can add these lines to composer.json');
        $this->info('"test": "@php artisan test",');
        $this->info('"test-coverage": "@php artisan test --parallel --coverage",');
        $this->info('"analyse": "vendor/bin/phpstan analyse --memory-limit=2G",');
        $this->info('"format": "vendor/bin/pint",');
        $this->info('"refactor": "vendor/bin/rector"');
        $this->info("\nRemember to run: `php artisan boost:install` in order install Laravel Boost MCP Server.");

        return self::SUCCESS;
    }
}
