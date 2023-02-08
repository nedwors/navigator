# Changelog

All notable changes to `navigator` will be documented in this file

## Unreleased

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
