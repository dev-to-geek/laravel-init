# Changelog

All notable changes to `laravel-init` will be documented in this file.

## v0.2.0 - 2026-02-09

### Breaking Changes

- Dropped support for Laravel 10 and Laravel 11. Minimum required version is now **Laravel 12**.
- Dropped support for PHP < 8.3. Minimum required version is now **PHP 8.3**.
- `minimum-stability` changed from `dev` to `stable`.
- Removed `spatie/laravel-package-tools` dependency.

### New Features

- **`--only` flag** — Install only specific tools: `php artisan laravel-init:install --only=pint --only=rector` (#23)
- **`--force` flag** — Overwrite existing configuration files instead of skipping them (#23)

### Bug Fixes

- **Fixed `--remove-me` flow** — Self-removal now executes after Laravel Boost installation and `composer update`, preventing broken state mid-installation (#23)

### Improvements

- Upgraded to **Pest v4** and **Orchestra Testbench v10** (#22)
- Refactored `InstallCommand`: extracted `runComposerStep()`, `installToolWithStub()`, `installPest()` methods, reducing `handle()` from ~165 to ~40 lines (#23)
- Removed dead code: empty `LaravelInit` class, unused `UnitTestCase`, empty config file, stale autoload paths (#23)
- Cleaned `TestCase.php`: removed unused Factory guessing and database setup boilerplate (#23)
- Updated command description to list all 6 tools (Pint, PHPStan, Pest, Pail, Rector, Laravel Boost) (#23)
- Replaced deprecated Rector `strictBooleans` set with `codingStyle` (#23)
- Cleaned `phpstan.neon.dist`: removed empty includes and commented-out blocks (#23)

### Tests

- **39 tests, 95 assertions** (up from 28 tests, 61 assertions)
- Added arch tests: strict types, command extends `Command`, no `env()` calls
- Added `ServiceProviderTest` for command registration
- Added `--remove-me` failure and execution order tests
- Added `--only` and `--force` feature tests

### CI

- Added `ramsey/composer-install` caching to test workflow
- Fixed PHPStan workflow: trigger on `phpstan.neon.dist`, run on PHP 8.3 (minimum supported)
- Added new **Rector dry-run** workflow

### Dependencies

- build(deps): bump dependabot/fetch-metadata from 2.4.0 to 2.5.0 (#20)

**Full Changelog**: https://github.com/dev-to-geek/laravel-init/compare/v0.1.6...v0.2.0

## v0.1.6 - 2025-08-14

### What's Changed

* build(deps): bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot[bot] in https://github.com/dev-to-geek/laravel-init/pull/9
* build(deps): bump aglipanci/laravel-pint-action from 2.5 to 2.6 by @dependabot[bot] in https://github.com/dev-to-geek/laravel-init/pull/12
* Feature/laravel boost by @mirchaemanuel in https://github.com/dev-to-geek/laravel-init/pull/14

**Full Changelog**: https://github.com/dev-to-geek/laravel-init/compare/v0.1.5...v0.1.6

## v0.1.4 - 2025-01-24

included larastan directive in phpstan.neon.dist stub file

### What's Changed

* chore: fixed phpstan.neon.dist.stub with larastan include by @mirchaemanuel in https://github.com/dev-to-geek/laravel-init/pull/1

### New Contributors

* @mirchaemanuel made their first contribution in https://github.com/dev-to-geek/laravel-init/pull/1

**Full Changelog**: https://github.com/dev-to-geek/laravel-init/compare/0.1.3...v0.1.4

## Added Rector installation - 2025-01-07

**Full Changelog**: https://github.com/dev-to-geek/laravel-init/compare/0.1.2...0.1.3

## v0.1.2 - 2024-12-13

full test coverage

## fixed path of phpstan analysis  - 2024-11-28

**Full Changelog**: https://github.com/dev-to-geek/laravel-init/compare/0.1.0...0.1.1

## 0.1.0 - 2024-11-28

A little package I use to add preconfigured tools (PHPStan, Larastan, Pint, Pail, ...) to my projects.
