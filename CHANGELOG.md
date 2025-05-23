# Changelog

All notable changes to `navigator` will be documented in this file

## Unreleased

## [1.0.1] - 2025-05-15
### Fixed
- Constrained Laravel to <=12.9.2 to avoid breaking changes in 12.10.0

## [1.0.0] - 2025-03-19
### Added
- Added support for PHP 8.3 and 8.4
- Added support for Laravel 12
- Added support for Pest 3
- Added Laravel Pint

### Removed
- Removed support for PHP 8.1 and below
- Removed support for Laravel 10 and below
- Removed support for Pest 2
- Removed PHP CS Fixer

## 0.8.0 2025-01-02
### Added
- Added `attributes` in `toArray`

## 0.7.0 2024-03-18
### Added
- Added support for Laravel 11, PHP 8.2, 8.3

### Fixed
- Fixed static analysis issue with Closure for Nav Facade

## 0.6.0 2023-02-08
### Added
- Added support for Laravel 10 and Pest v2

## 0.5.0 2022-12-29
### Added
- Added support for `hasActiveDescendants` in `toArray` and `toJson`

### Fixed
- Fixed spelling for descendants

## 0.4.1 2022-12-11
### Changed
- Updated types for `Nav::define`

## 0.4.0 2022-10-30
### Changed
- Enhanced `toArray` and `toJson`

### Added
- Added `active` output to `toArray` and `toJson`

## 0.3.0 - 2022-02-21
### Added
- Support for Laravel 9
- Support for PHP 8.1

### Changed
- Refactored to core `Support` `Collection` rather than `LazyCollection`. As such, removed support for generators.

## 0.2.0 - 2021-11-25

### Added
- Support to retrieve Nav items as json

### Changed
- Updated default item route to `null` from `#0`
