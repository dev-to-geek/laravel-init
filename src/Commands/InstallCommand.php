<?php

declare(strict_types=1);

namespace Dev2Geek\LaravelInit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    public $signature = 'laravel-init:install {--force : Overwrite existing configuration files without confirmation}';

    public $description = 'Install Pint, PhpStan, Pest, Pail.';

    /**
     * Collection of errors that occurred during installation.
     *
     * @var array<int, array{package: string, command: string, output: string, error: string}>
     */
    protected array $errors = [];

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

            // Only install Livewire plugin if Livewire is already installed
            if ($this->isPackageInstalled('livewire/livewire')) {
                $this->runComposerCommand('composer require pestphp/pest-plugin-livewire --dev -n', '❌ Failed to install pest plugin livewire');
            } else {
                $this->warn('⚠️  Skipping pest-plugin-livewire (Livewire not detected in your project)');
            }
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

        // Show error summary if there were any errors
        if (count($this->errors) > 0) {
            $this->showErrorSummary();

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Execute a composer command and register error if it fails.
     * Does not stop execution - continues with other packages.
     */
    protected function runComposerCommand(string $command, string $errorMessage): void
    {
        $process = Process::run($command);

        if ($process->failed()) {
            // Register the error for later display
            $this->errors[] = [
                'package' => $errorMessage,
                'command' => $command,
                'output' => $process->output(),
                'error' => $process->errorOutput(),
            ];

            // Show immediate feedback but don't stop
            $this->warn($errorMessage);
        }
    }

    /**
     * Check if a package is installed in the current project.
     */
    protected function isPackageInstalled(string $packageName): bool
    {
        $composerJsonPath = base_path('composer.json');

        if (! File::exists($composerJsonPath)) {
            return false;
        }

        $composerJson = json_decode(File::get($composerJsonPath), true);

        if (! $composerJson) {
            return false;
        }

        // Check both require and require-dev sections
        return isset($composerJson['require'][$packageName])
            || isset($composerJson['require-dev'][$packageName]);
    }

    /**
     * Copy a stub configuration file to the project root.
     * Asks for confirmation if the file already exists (unless --force is used).
     */
    protected function copyStubFile(string $stubFileName, string $destinationFileName): void
    {
        $source = __DIR__."/../../stubs/{$stubFileName}";
        $destination = base_path($destinationFileName);

        // Check if file already exists
        if (File::exists($destination)) {
            // If --force option is not set, ask for confirmation
            if (! $this->option('force')) {
                $shouldOverwrite = confirm(
                    label: "File {$destinationFileName} already exists. Overwrite?",
                    default: false
                );

                if (! $shouldOverwrite) {
                    $this->warn("⚠️  Skipping {$destinationFileName} (file already exists)");

                    return;
                }
            }

            // User confirmed or --force is set
            $this->info("Overwriting {$destinationFileName}...");
        }

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

    /**
     * Display a detailed summary of all errors that occurred during installation.
     */
    protected function showErrorSummary(): void
    {
        $this->newLine();
        $this->error('═══════════════════════════════════════════════════════════════');
        $this->error('  ❌ INSTALLATION ERRORS SUMMARY');
        $this->error('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->warn(sprintf('Total errors: %d', count($this->errors)));
        $this->newLine();

        foreach ($this->errors as $index => $error) {
            $errorNumber = $index + 1;
            $this->error("─────────────────────────────────────────────────────────────────");
            $this->error("Error #{$errorNumber}: {$error['package']}");
            $this->error("─────────────────────────────────────────────────────────────────");
            $this->line("<fg=gray>Command:</> {$error['command']}");
            $this->newLine();

            // Show composer output if available
            if (! empty($error['output'])) {
                $this->line('<fg=yellow>Output:</>');
                $this->line($error['output']);
                $this->newLine();
            }

            // Show error output if available
            if (! empty($error['error'])) {
                $this->line('<fg=red>Error Output:</>');
                $this->line($error['error']);
                $this->newLine();
            }
        }

        $this->error('═══════════════════════════════════════════════════════════════');
        $this->newLine();
        $this->line('💡 <fg=yellow>Tip:</> Review the error messages above to resolve the issues.');
        $this->line('   You can re-run the command after fixing the problems.');
    }
}
