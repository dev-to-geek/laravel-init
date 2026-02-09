<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    private const array VALID_TOOLS = ['pint', 'larastan', 'pest', 'pail', 'rector', 'boost'];

    public $signature = 'laravel-init:install
        {--remove-me : Remove the laravel-init package after installation}
        {--only=* : Install only specific tools (pint, larastan, pest, pail, rector, boost)}
        {--force : Overwrite existing configuration files}';

    public $description = 'Install Pint, PhpStan, Pest, Pail, Rector, and Laravel Boost.';

    public function handle(): int
    {
        /** @var array<int, string> $only */
        $only = $this->option('only');
        $force = (bool) $this->option('force');

        if ($only !== []) {
            $invalid = array_diff($only, self::VALID_TOOLS);
            if ($invalid !== []) {
                $this->error('❌ Invalid tool(s): '.implode(', ', $invalid).'. Valid tools: '.implode(', ', self::VALID_TOOLS));

                return self::FAILURE;
            }
        }

        if ($only === [] || in_array('pint', $only, true)) {
            $this->installToolWithStub(
                'composer require laravel/pint --dev -n',
                'Installing pint...',
                'Failed to install pint',
                'pint.json.stub',
                'pint.json',
                'Failed to copy pint configuration file',
                $force,
            );
            $this->info('✅ Pint installed successfully');
        }

        if ($only === [] || in_array('larastan', $only, true)) {
            $this->installToolWithStub(
                'composer require --dev "larastan/larastan:^3.1" -n',
                'Installing larastan...',
                'Failed to install larastan',
                'phpstan.neon.dist.stub',
                'phpstan.neon.dist',
                'Failed to copy phpstan configuration file',
                $force,
            );
            $this->info('✅ Larastan installed successfully');
        }

        if ($only === [] || in_array('pest', $only, true)) {
            $this->installPest();
            $this->info('✅ pestphp installed successfully');
        }

        if ($only === [] || in_array('pail', $only, true)) {
            $this->runComposerStep('composer require laravel/pail -n', 'Installing pail...', 'Failed to install pail');
            $this->info('✅ Pail installed successfully');
        }

        if ($only === [] || in_array('rector', $only, true)) {
            $this->installToolWithStub(
                'composer require rector/rector -n --dev',
                'Installing rector...',
                'Failed to install rector',
                'rector.php.stub',
                'rector.php',
                'Failed to copy rector configuration file',
                $force,
            );
            $this->info('✅ Rector installed successfully');
        }

        if ($only === [] || in_array('boost', $only, true)) {
            $this->runComposerStep('composer require laravel/boost --dev -n', 'Installing laravel boost...', 'Failed to install laravel boost');
            $this->info('✅ Laravel Boost installed successfully');
        }

        $this->runComposerStep('composer update -Wn', 'Updating composer...', 'Failed to update composer');
        $this->info('✅ Composer updated successfully');

        if ($this->option('remove-me')) {
            $this->runComposerStep('composer remove dev-to-geek/laravel-init -n', 'Removing laravel-init...', 'Failed to remove laravel-init');

            $this->info('So long, and thanks for all the fish!');
            $this->info('✅ laravel-init removed successfully');

            return self::SUCCESS;
        }

        $this->info('For your convenience, you can add these lines to composer.json');
        $this->info('"test": "@php artisan test",');
        $this->info('"test-coverage": "@php artisan test --parallel --coverage",');
        $this->info('"analyse": "vendor/bin/phpstan analyse --memory-limit=2G",');
        $this->info('"format": "vendor/bin/pint",');
        $this->info('"refactor": "vendor/bin/rector"');
        $this->info("\nRemember to run: `php artisan boost:install` in order install Laravel Boost MCP Server.");

        return self::SUCCESS;
    }

    private function runComposerStep(string $command, string $spinMessage, string $failMessage): void
    {
        spin(
            callback: function () use ($command, $failMessage): void {
                $process = Process::run($command);
                if ($process->failed()) {
                    self::fail('❌ '.$failMessage);
                }
            },
            message: $spinMessage,
        );
    }

    private function installToolWithStub(string $command, string $spinMessage, string $failMessage, string $stub, string $target, string $copyFailMessage, bool $force): void
    {
        spin(
            callback: function () use ($command, $failMessage, $stub, $target, $copyFailMessage, $force): void {
                $process = Process::run($command);
                if ($process->failed()) {
                    self::fail('❌ '.$failMessage);
                }

                $targetPath = base_path($target);

                if (! $force && File::exists($targetPath)) {
                    return;
                }

                $result = File::copy(__DIR__.'/../../stubs/'.$stub, $targetPath);
                if (! $result) {
                    self::fail('❌ '.$copyFailMessage);
                }
            },
            message: $spinMessage,
        );
    }

    private function installPest(): void
    {
        spin(
            callback: function (): void {
                Process::run('composer remove phpunit/phpunit -n');

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

                $process = Process::run('composer require pestphp/pest-plugin-laravel --dev -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install pest plugin laravel');
                }

                $process = Process::run('composer require pestphp/pest-plugin-livewire --dev -n');
                if ($process->failed()) {
                    self::fail('❌ Failed to install pest plugin livewire');
                }
            },
            message: 'Installing pestphp and plugins...',
        );
    }
}
