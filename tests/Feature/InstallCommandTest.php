<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

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
        ->expectsOutputToContain('pestphp installed successfully')
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
        ->expectsOutputToContain('Failed to copy pint configuration file')
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
        ->expectsOutputToContain('Failed to copy phpstan configuration file')
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
        ->expectsOutputToContain('Failed to copy rector configuration file')
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

it('installs and inits pest with the right commands', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue();
    Process::fake();

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer remove phpunit/phpunit -n');
    Process::assertRan('composer require pestphp/pest --dev --with-all-dependencies -n');
    Process::assertRan('./vendor/bin/pest --init');
    Process::assertRan('composer require mockery/mockery --dev -n');
    Process::assertRan('composer require pestphp/pest-plugin-laravel --dev -n');
    Process::assertRan('composer require pestphp/pest-plugin-livewire --dev -n');
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

it('fails if cannot install pest plugin livewire', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

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

it('accepts remove-me option', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act
    $this->artisan('laravel-init:install --remove-me')
        ->assertExitCode(0);
});

it('removes itself from composer if option remove-me is true', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act
    $this->artisan('laravel-init:install --remove-me');

    // Assert
    Process::assertRan('composer remove dev-to-geek/laravel-init -n');

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

it('fails if cannot remove itself', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer remove dev-to-geek/laravel-init -n' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result(
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install --remove-me')
        ->expectsOutputToContain('Failed to remove laravel-init')
        ->assertExitCode(1);
});

it('executes remove-me after boost and composer update', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act
    $this->artisan('laravel-init:install --remove-me');

    // Assert - verify all expected commands ran
    Process::assertRan('composer require laravel/boost --dev -n');
    Process::assertRan('composer update -Wn');
    Process::assertRan('composer remove dev-to-geek/laravel-init -n');
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

it('installs only pint when --only=pint is passed', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act
    $this->artisan('laravel-init:install --only=pint')
        ->expectsOutputToContain('Pint installed successfully')
        ->assertExitCode(0);

    // Assert
    Process::assertRan('composer require laravel/pint --dev -n');
    Process::assertDidntRun('composer require --dev "larastan/larastan:^3.1" -n');
    Process::assertDidntRun('composer require pestphp/pest --dev --with-all-dependencies -n');
    Process::assertDidntRun('composer require laravel/pail -n');
    Process::assertDidntRun('composer require rector/rector -n --dev');
    Process::assertDidntRun('composer require laravel/boost --dev -n');
    Process::assertRan('composer update -Wn');
});

it('installs multiple tools when --only is passed multiple times', function (): void {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act
    $this->artisan('laravel-init:install --only=pint --only=rector')
        ->expectsOutputToContain('Pint installed successfully')
        ->expectsOutputToContain('Rector installed successfully')
        ->assertExitCode(0);

    // Assert
    Process::assertRan('composer require laravel/pint --dev -n');
    Process::assertRan('composer require rector/rector -n --dev');
    Process::assertDidntRun('composer require --dev "larastan/larastan:^3.1" -n');
    Process::assertDidntRun('composer require pestphp/pest --dev --with-all-dependencies -n');
});

it('fails with invalid tool name in --only', function (): void {
    // Arrange
    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install --only=invalid')
        ->expectsOutputToContain('Invalid tool(s): invalid')
        ->assertExitCode(1);
});

it('skips config copy when file exists and --force is not passed', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturn(true);
    File::shouldReceive('copy')
        ->never();
    Process::fake();

    // Act
    $this->artisan('laravel-init:install --only=pint')
        ->expectsOutputToContain('Pint installed successfully')
        ->assertExitCode(0);

    // Assert
    Process::assertRan('composer require laravel/pint --dev -n');
});

it('overwrites config when file exists and --force is passed', function (): void {
    // Arrange
    File::shouldReceive('exists')
        ->andReturn(true);
    File::shouldReceive('copy')
        ->andReturn(true);
    Process::fake();

    // Act
    $this->artisan('laravel-init:install --only=pint --force')
        ->expectsOutputToContain('Pint installed successfully')
        ->assertExitCode(0);

    // Assert
    $expectedSource = realpath(__DIR__.'/../../stubs/pint.json.stub');

    File::shouldHaveReceived('copy')
        ->withArgs(fn ($source, $destination): bool => realpath($source) === $expectedSource &&
            str_ends_with((string) $destination, 'pint.json'))
        ->once();
});
