<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

it('can run laravel-init:install command', function () {

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

it('fails if cannot install pint', function () {

    // Arrange
    Process::fake([
        'composer require laravel/pint*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( //fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install pint')
        ->assertExitCode(1);
});

it('installs pint with the right command', function () {
    // Arrange
    File::shouldReceive('exists')
        ->andReturn(false);
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require laravel/pint --dev -n' => Process::result(
            exitCode: 0
        ),
        '*' => Process::result( //fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer require laravel/pint --dev -n');
});

it('copy pint stub configuration file', function () {
    // Arrange
    File::spy();
    File::shouldReceive()
        ->copy()
        ->andReturn(true);

    Process::fake();

    $expectedSource = realpath(__DIR__.'/../../stubs/pint.json.stub');

    // Act & Assert
    $this->artisan('laravel-init:install');

    File::shouldHaveReceived('copy')
        ->withArgs(function ($source, $destination) use ($expectedSource) {
            return realpath($source) === $expectedSource &&
                str_ends_with($destination, 'pint.json');
        })
        ->once();
});

it('fails if cannot copy pint stub configuration file', function () {
    // Arrange
    File::shouldReceive('copy')
        ->andReturn(false);

    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to copy pint configuration file')
        ->assertExitCode(1);

});

it('fails if cannot install larastan', function () {

    // Arrange
    File::shouldReceive('exists')
        ->andReturn(false);
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require --dev "larastan/larastan:*' => Process::result(
            exitCode: 1
        ),
        '*' => Process::result( //fake all other commands
            exitCode: 0
        ),
    ]);

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to install larastan')
        ->assertExitCode(1);
});

it('installs larastan with the right command', function () {
    // Arrange
    File::shouldReceive('exists')
        ->andReturn(false);
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake([
        'composer require --dev "larastan/larastan:^3.0" -n' => Process::result(
            exitCode: 0
        ),
        '*' => Process::result( //fake all other commands
            exitCode: 0
        ),
    ]);

    // Act
    $this->artisan('laravel-init:install');

    // Assert
    Process::assertRan('composer require --dev "larastan/larastan:^3.0" -n');
});

it('copy larastan stub configuration file', function () {
    // Arrange
    File::spy();
    File::shouldReceive('copy')
        ->andReturn(true);

    Process::fake();

    $expectedSource = realpath(__DIR__.'/../../stubs/phpstan.neon.dist.stub');

    // Act & Assert
    $this->artisan('laravel-init:install');

    File::shouldHaveReceived('copy')
        ->withArgs(function ($source, $destination) use ($expectedSource) {
            return realpath($source) === $expectedSource &&
                str_ends_with($destination, 'phpstan.neon.dist');
        })
        ->once();
});

it('fails if cannot copy phpstan stub configuration file', function () {
    // Arrange
    File::shouldReceive('copy')
        ->andReturnTrue()
        ->once();

    File::shouldReceive('copy')
        ->andReturnFalse()
        ->times(1);

    Process::fake();

    // Act & Assert
    $this->artisan('laravel-init:install')
        ->expectsOutputToContain('Failed to copy phpstan configuration file')
        ->assertExitCode(1);

});
