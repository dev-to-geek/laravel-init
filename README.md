# A little package I use to add preconfigured tools (PHPStan, Larastan, Pint, Pail, ...) to my projects

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dev-to-geek/laravel-init.svg?style=flat-square)](https://packagist.org/packages/dev-to-geek/laravel-init)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/dev-to-geek/laravel-init/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/dev-to-geek/laravel-init/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/dev-to-geek/laravel-init/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/dev-to-geek/laravel-init/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dev-to-geek/laravel-init.svg?style=flat-square)](https://packagist.org/packages/dev-to-geek/laravel-init)

A little package I use to add preconfigured tools (PHPStan, Larastan, Pint, Pail, ...) to my projects.

## Installation

You can install the package via composer:

```bash
composer require dev-to-geek/laravel-init
```

## Usage

```php
php artisan laravel-init:install [--remove-me]
```

This command will automatically install and configure the following tools for your Laravel project:

- **Laravel Pint** – Automated code formatting.
- **PHPStan & Larastan** – Static analysis for improved code quality.
- **Pest PHP** (with Mockery and plugins) – Modern testing framework with mocking support.
- **Laravel Pail** – Enhanced logging and debugging utilities.
- **Rector** – Automated code refactoring.
- **Laravel Boost** – Additional productivity enhancements.

No manual setup is required; everything is ready to use after running the command.

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
