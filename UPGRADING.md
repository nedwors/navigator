# Upgrade Guide

## 1.0.0
Navigator no longer supports the following:
- Laravel 10 and below
- PHP 8.1 and below

Ensure to upgrade your application to these technologies before upgrading to this version.

## 0.3.0
- Navigator no longer uses `LazyCollections`, instead it uses the core `Collection` class. As such, ensure all references are updated as applicable. As such, PHP generator support has been removed.
