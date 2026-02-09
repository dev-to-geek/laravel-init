# Laravel Init

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dev-to-geek/laravel-init.svg?style=flat-square)](https://packagist.org/packages/dev-to-geek/laravel-init)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/dev-to-geek/laravel-init/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/dev-to-geek/laravel-init/actions?query=workflow%3Arun-tests+branch%3Amain)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/dev-to-geek/laravel-init/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/dev-to-geek/laravel-init/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dev-to-geek/laravel-init.svg?style=flat-square)](https://packagist.org/packages/dev-to-geek/laravel-init)

A Laravel package that bootstraps your project with preconfigured development tools via a single artisan command.

## Requirements

- PHP ^8.3
- Laravel ^12.0

## Installation

```bash
composer require dev-to-geek/laravel-init --dev
```

## Usage

```bash
php artisan laravel-init:install
```

This command will automatically install and configure the following tools:

- **Laravel Pint** -- Automated code formatting (with `pint.json` config).
- **Larastan** -- PHPStan + Larastan static analysis (with `phpstan.neon.dist` config).
- **Pest PHP** -- Modern testing framework with Mockery, Laravel and Livewire plugins.
- **Laravel Pail** -- Real-time log viewer.
- **Rector** -- Automated code refactoring (with `rector.php` config).
- **Laravel Boost** -- MCP Server for AI-assisted development.

No manual setup is required; everything is ready to use after running the command.

### Options

#### `--only` -- Install specific tools

Install only the tools you need instead of the full suite:

```bash
# Install only Pint and Larastan
php artisan laravel-init:install --only=pint --only=larastan

# Install only Pest
php artisan laravel-init:install --only=pest
```

Valid tool names: `pint`, `larastan`, `pest`, `pail`, `rector`, `boost`.

#### `--force` -- Overwrite existing configuration files

By default, existing configuration files (`pint.json`, `phpstan.neon.dist`, `rector.php`) are not overwritten. Use `--force` to replace them:

```bash
php artisan laravel-init:install --force
```

#### `--remove-me` -- Self-uninstall after setup

Remove the `laravel-init` package from your project after all tools have been installed:

```bash
php artisan laravel-init:install --remove-me
```

### Suggested composer scripts

After installation, you can add these scripts to your `composer.json`:

```json
"scripts": {
    "test": "@php artisan test",
    "test-coverage": "@php artisan test --parallel --coverage",
    "analyse": "vendor/bin/phpstan analyse --memory-limit=2G",
    "format": "vendor/bin/pint",
    "refactor": "vendor/bin/rector"
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mircha Emanuel D'Angelo](https://github.com/mirchaemanuel)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
