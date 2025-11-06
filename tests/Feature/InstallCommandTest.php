<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

beforeEach(function (): void {
    File::spy();
});

it('can run laravel-init:install command', function (): void {

    // Arrange
    File::shouldReceive('exists')
        ->andReturn(false);
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        '*' => Process::result(
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Pint installed successfully')
        ->expectsOutputToContain('Larastan installed successfully')
        ->expectsOutputToContain('pestphp and plugins installed successfully')
        ->expectsOutputToContain('Pail installed successfully')
        ->assertExitCode(0);

});

it('fails if cannot install pint', function (): void {
    // Arrange
    Process::fake([
        'composer require laravel/pint*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install pint')
        ->assertExitCode(1);
});

it('installs pint with the right command', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturn(false);
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require laravel/pint --dev -n' => Process::result(
            exitCode: 0
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer require laravel/pint --dev -n');
});

it('copy pint stub configuration file', function (): void {
    // Arrange
    File::shouldReceive()
        ->copy()
        ->andReturn(true);

    Process::fake();

    $expectedSource = realpath(__DIR__.'/../../stubs/pint.json.stub');

    // Act & Assert
    $this->artisan('laravel-init:install');

    File::shouldHaveReceived('copy')
        ->withArgs(fn ($source, $destination): bool => realpath($source) === $expectedSource &&
            str_ends_with((string) $destination, 'pint.json'))
        ->once();
});

it('fails if cannot copy pint stub configuration file', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(false);
    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to copy pint.json configuration file')
        ->assertExitCode(1);

});

it('fails if cannot install larastan', function (): void {

    // Arrange
    File::shouldReceive('exists')
        ->andReturn(false);
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require --dev "larastan/larastan:*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install larastan')
        ->assertExitCode(1);
});

it('installs larastan with the right command', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturn(false);
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require --dev "larastan/larastan:^3.1" -n' => Process::result(
            exitCode: 0
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer require --dev "larastan/larastan:^3.1" -n');
});

it('copy larastan stub configuration file', function (): void {
    // Arrange
    File::spy();
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    $expectedSource = realpath(__DIR__.'/../../stubs/phpstan.neon.dist.stub');

    // Act & Assert
    $this->artisan('laravel-init:install');

    File::shouldHaveReceived('copy')
        ->withArgs(fn ($source, $destination): bool => realpath($source) === $expectedSource &&
            str_ends_with((string) $destination, 'phpstan.neon.dist'))
        ->once();
});

it('fails if cannot copy phpstan stub configuration file', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->withArgs(fn ($source, $destination) => Str::of($source)->endsWith('phpstan.neon.dist.stub'))
        ->andReturnFalse()
        ->once();

    File::shouldReceive('copy')
        ->andReturnTrue();
    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to copy phpstan.neon.dist configuration file')
        ->assertExitCode(1);
});

it('fails if cannot install pest', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue();
    Process::fake([
        'composer require pestphp/pest*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install pestphp')
        ->assertExitCode(1);
});

it('fails if cannot install rector', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue();
    Process::fake([
        'composer require rector/rector*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install rector')
        ->assertExitCode(1);
});

it('fails if cannot copy rector configuration file', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->withArgs(fn ($source, $destination) => Str::of($source)->endsWith('rector.php.stub'))
        ->andReturnFalse()
        ->once();

    File::shouldReceive('copy')
        ->andReturnTrue();
    Process::fake();
    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to copy rector.php configuration file')
        ->assertExitCode(1);
});

it('removes phpunit before to install pest', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue();
    Process::fake();

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer remove phpunit/phpunit -n');
    Process::assertRan('composer require pestphp/pest --dev --with-all-dependencies -n');
});

it('installs and inits pest with the right commands when livewire is not installed', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue();
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturnTrue();
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));

    Process::fake();

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer remove phpunit/phpunit -n');
    Process::assertRan('composer require pestphp/pest --dev --with-all-dependencies -n');
    Process::assertRan('./vendor/bin/pest --init');
    Process::assertRan('composer require mockery/mockery --dev -n');
    Process::assertRan('composer require pestphp/pest-plugin-faker --dev -n');
    Process::assertRan('composer require pestphp/pest-plugin-laravel --dev -n');
    Process::assertNotRan('composer require pestphp/pest-plugin-livewire --dev -n');
});

it('installs pest-plugin-livewire when livewire is installed', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue();
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturnTrue();
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode([
            'require' => ['livewire/livewire' => '^3.0'],
            'require-dev' => [],
        ]));

    Process::fake();

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer require pestphp/pest-plugin-livewire --dev -n');
});

it('shows warning when skipping livewire plugin', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue();
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturnTrue();
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));

    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Skipping pest-plugin-livewire')
        ->assertExitCode(0);
});

it('fails if cannot init pest', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        './vendor/bin/pest --init' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install')
        ->assertExitCode(1)
        ->expectsOutputToContain('Failed to init pest');

});

it('fails if cannot install mockery', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require mockery/mockery --dev*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install')
        ->assertExitCode(1)
        ->expectsOutputToContain('Failed to install mockery');

});

it('fails if cannot install faker', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require pestphp/pest-plugin-faker --dev*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install')
        ->assertExitCode(1)
        ->expectsOutputToContain('Failed to install pest plugin faker');

});

it('fails if cannot install pest plugin laravel', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require pestphp/pest-plugin-laravel --dev*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install')
        ->assertExitCode(1)
        ->expectsOutputToContain('Failed to install pest plugin laravel');

});

it('fails if cannot install pest plugin livewire when livewire is installed', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturnTrue();
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode([
            'require' => ['livewire/livewire' => '^3.0'],
            'require-dev' => [],
        ]));

    Process::fake([
        'composer require pestphp/pest-plugin-livewire --dev*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install')
        ->assertExitCode(1)
        ->expectsOutputToContain('Failed to install pest plugin livewire');

});

it('installs pail with the right command', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require laravel/pail -n' => Process::result(
            exitCode: 0
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer require laravel/pail -n');
});

it('fails if cannot install pail', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require laravel/pail -n' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install pail')
        ->assertExitCode(1);
});

it('installs laravel boost with the right command', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require laravel/boost --dev -n' => Process::result(
            exitCode: 0
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer require laravel/boost --dev -n');
});

it('fails if cannot install laravel boost', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require laravel/boost --dev -n' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install laravel boost')
        ->assertExitCode(1);
});

it('runs composer update command', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer update -Wn');
});

it('fails if cannot run composer update', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer update -Wn' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( // fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to update composer')
        ->assertExitCode(1);
});

it('shows a snippet to add to composer.json', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutput('For your convenience, you can add these lines to composer.json')
        ->expectsOutput('"test": "@php artisan test",')
        ->expectsOutput('"test-coverage": "@php artisan test --parallel --coverage",')
        ->expectsOutput('"analyse": "vendor/bin/phpstan analyse --memory-limit=2G",')
        ->expectsOutput('"format": "vendor/bin/pint",')
        ->expectsOutput('"refactor": "vendor/bin/rector"');

});

it('skips existing config file when user declines to overwrite', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturnTrue();
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));
    File::shouldReceive('exists')
        ->with(base_path('pint.json'))
        ->andReturnTrue(); // pint.json already exists
    File::shouldReceive('exists')
        ->with(base_path('phpstan.neon.dist'))
        ->andReturnFalse();
    File::shouldReceive('exists')
        ->with(base_path('rector.php'))
        ->andReturnFalse();
    File::shouldReceive('copy')
        ->with(Mockery::type('string'), base_path('phpstan.neon.dist'))
        ->andReturnTrue();
    File::shouldReceive('copy')
        ->with(Mockery::type('string'), base_path('rector.php'))
        ->andReturnTrue();
    // pint.json should NOT be copied

    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsConfirmation('File pint.json already exists. Overwrite?', 'no')
        ->expectsOutputToContain('Skipping pint.json')
        ->assertExitCode(0);
});

it('overwrites existing config file when user confirms', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturnTrue();
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));
    File::shouldReceive('exists')
        ->with(base_path('pint.json'))
        ->andReturnTrue(); // pint.json already exists
    File::shouldReceive('exists')
        ->with(base_path('phpstan.neon.dist'))
        ->andReturnFalse();
    File::shouldReceive('exists')
        ->with(base_path('rector.php'))
        ->andReturnFalse();
    File::shouldReceive('copy')
        ->andReturnTrue(); // All copies succeed

    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsConfirmation('File pint.json already exists. Overwrite?', 'yes')
        ->expectsOutputToContain('Overwriting pint.json')
        ->assertExitCode(0);
});

it('overwrites existing config file with --force without asking', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturnTrue();
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));
    File::shouldReceive('exists')
        ->with(base_path('pint.json'))
        ->andReturnTrue(); // pint.json already exists
    File::shouldReceive('exists')
        ->with(base_path('phpstan.neon.dist'))
        ->andReturnFalse();
    File::shouldReceive('exists')
        ->with(base_path('rector.php'))
        ->andReturnFalse();
    File::shouldReceive('copy')
        ->andReturnTrue(); // Should copy with --force

    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install --force')
        ->expectsOutputToContain('Overwriting pint.json')
        ->assertExitCode(0);
});

it('copies new config file without prompting when file does not exist', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturnFalse(); // No files exist
    File::shouldReceive('copy')
        ->andReturnTrue();
    File::shouldReceive('get')
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));

    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->doesntExpectOutput('already exists') // No prompts since files don't exist
        ->assertExitCode(0);
});

it('continues installation when one package fails and shows error summary', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturnFalse();
    File::shouldReceive('get')
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));
    File::shouldReceive('copy')
        ->andReturnTrue();

    Process::fake([
        'composer require laravel/pint --dev -n' => Process::result(
            output: 'Some output from composer',
            errorOutput: 'Pint installation failed - dependency conflict',
            exitCode: 1
        ),
        '*' => Process::result(exitCode: 0), // All other commands succeed
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install pint') // Immediate warning
        ->expectsOutputToContain('Larastan installed successfully') // Continues with next package
        ->expectsOutputToContain('INSTALLATION ERRORS SUMMARY') // Shows summary at end
        ->expectsOutputToContain('Total errors: 1')
        ->expectsOutputToContain('composer require laravel/pint')
        ->expectsOutputToContain('Pint installation failed')
        ->assertExitCode(1); // Returns failure code
});

it('shows detailed error information for multiple failures', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturnFalse();
    File::shouldReceive('get')
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));
    File::shouldReceive('copy')
        ->andReturnTrue();

    Process::fake([
        'composer require laravel/pint --dev -n' => Process::result(
            output: 'Pint output',
            errorOutput: 'Pint error details',
            exitCode: 1
        ),
        'composer require --dev "larastan/larastan:^3.1" -n' => Process::result(
            output: 'Larastan output',
            errorOutput: 'Larastan error details',
            exitCode: 1
        ),
        '*' => Process::result(exitCode: 0),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Total errors: 2')
        ->expectsOutputToContain('Pint error details')
        ->expectsOutputToContain('Larastan error details')
        ->assertExitCode(1);
});

it('returns success when no errors occur', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturnFalse();
    File::shouldReceive('get')
        ->andReturn(json_encode(['require' => [], 'require-dev' => []]));
    File::shouldReceive('copy')
        ->andReturnTrue();
    Process::fake(); // All commands succeed

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->doesntExpectOutput('INSTALLATION ERRORS SUMMARY') // No error summary
        ->assertExitCode(0); // Returns success
});
