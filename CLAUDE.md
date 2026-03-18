# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

`dev-to-geek/laravel-init` is a Laravel package that bootstraps Laravel projects with preconfigured development tools (Pint, Larastan, Pest, Pail, Rector, Laravel Boost) via a single artisan command: `php artisan laravel-init:install`.

- **PHP:** ^8.3 | **Laravel:** ^12.0, ^13.0
- **Namespace:** `Dev2Geek\LaravelInit`
- All files must use `declare(strict_types=1)`

## Commands

```bash
composer test              # Run tests (Pest)
composer test-coverage     # Parallel tests with coverage
composer analyse           # PHPStan static analysis (level 10, 2G memory)
composer format            # Code formatting (Pint, Laravel preset)
composer refactor          # Rector automated refactoring
```

Run a single test file:
```bash
vendor/bin/pest tests/Feature/InstallCommandTest.php
```

Run a single test by name:
```bash
vendor/bin/pest --filter="can run laravel-init:install command"
```

## Architecture

The package has a minimal structure with one primary flow:

**ServiceProvider** (`src/LaravelInitServiceProvider.php`) registers a single artisan command and merges config.

**InstallCommand** (`src/Commands/InstallCommand.php`) is the core of the package. It sequentially installs tools via `Process::run()` composer commands, copies configuration stubs via `File::copy()`, and wraps each step in `spin()` for terminal feedback. Each step fails fast with `self::fail()` on error. The `--remove-me` flag triggers self-uninstallation.

**Stubs** (`stubs/`) contain configuration templates (pint.json, phpstan.neon.dist, rector.php) that get copied to the target project root during installation.

## Testing

Tests use **Pest** with **Orchestra Testbench**. The base `TestCase` (`tests/TestCase.php`) extends `Orchestra\Testbench\TestCase` and registers the package's service provider.

Testing patterns used throughout:
- `Process::fake()` with pattern matching to mock composer commands
- `File::spy()` / `File::shouldReceive()` for filesystem mocking
- `$this->artisan('laravel-init:install')` for command integration tests
- Arrange/Act/Assert structure in every test

Architecture tests (`tests/ArchTest.php`) enforce no debug functions (`dd`, `dump`, `ray`).

## Code Quality Standards

- **PHPStan level 10** (maximum) with Larastan — analyzes `src/` only
- **Pint** with Laravel preset plus strict rules: `strict_comparison`, `declare_strict_types`, `ordered_class_elements`, `protected_to_private`
- **Rector** runs on `src/`, `config/`, `tests/` with sets: deadCode, codeQuality, typeDeclarations, privatization, earlyReturn, strictBooleans
- Class element ordering: traits → constants → properties → constructor → magic → static methods → public → protected → private
