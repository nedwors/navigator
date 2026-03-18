# Upgrade Guide

## Unreleased

## 3.x
### PHP Support
This version supports PHP 8.3 and above. Support for PHP 8.2 and below has been dropped.

### Laravel Support
This version supports Laravel 13. Support for Laravel 12 and below has been dropped.

## 2.x
### `when` and `unless` methods
The `when` and `unless` methods have been renamed to `includeWhen` and `includeUnless` respectively.

Update your application accordingly:
```diff
Nav::item('Home')
-   ->when($someCondition)
+   ->includeWhen($someCondition)
...
Nav::item('Dashboard')
-   ->unless($someCondition)
+   ->includeUnless($someCondition)
```

## 1.x
Navigator no longer supports the following:
- Laravel 10 and below
- PHP 8.1 and below

Ensure to upgrade your application to these technologies before upgrading to this version.

## 0.3.0
- Navigator no longer uses `LazyCollections`, instead it uses the core `Collection` class. As such, ensure all references are updated as applicable. As such, PHP generator support has been removed.
