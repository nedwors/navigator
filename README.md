# Menui

Menui is a package to create headless menus for use in Laravel applications:

```php
// In a Service Provider

Menu::define(fn ($user) => [
    Menu::item('Dashboard')
        ->for('dashboard')
        ->icon('dashboard.svg')
        ->when($user->can('access.dashboard'))
])

// In your view

@foreach(Menu::items() as $item)
    //
@foreach
```
> This is a headless package so you are completely free to style as you see fit.

## Installation

You can install the package via composer:

```bash
composer require nedwors/laravel-menu
```

## Usage

Select a Service Provider - or perhaps make a dedicated one! - and pop the following in:
```php
Menu::define(fn () => [
    Menu::item('Dashboard')
        ->for('dashboard')
        ->icon('dashboard')
]);
```

Et voila! You now have a menu ready to use. You can retrieve the items as follows:

```blade
@foreach(Menu::items())
    //
@endforeach

// or

@foreach(menuitems())
    //
@endforeach
```
Now, you'll probably want more than just a dashboard link - then again maybe not, it's your app! - but hopefully you can see how easy it is to get up and running.

Let's dig further into the configuration available to you:

#### API
##### Name
The name is defined and retrieved as follows:

```php
Menu::item('Dashboard')

$item->name // Dashboard
```
> The name is passed into the Laravel `__()` lang helper before outputting.

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email nedwors@gmail.com instead of using the issue tracker.

## Credits

-   [Sam Rowden](https://github.com/nedwors)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
