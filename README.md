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

// In a view

@foreach(Menu::items() as $item)
    //
@endforeach
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

Let's dig further into the features available to you:

### API
The package consists of two main features - the `Menu` and the `Items` within. We will explore the `Item` and then the `Menu`.

It's worth noting at this point that `Item` extends `Illuminate\Support\Fluent` to allow for custom methods/properties on a per project basis. Also, `Menu` is macroable to again allow for custom functionality in your projects.

`Item`
- [Name](#name)
- [URL](#url)
- [Icons](#icons)
- [Conditionals](#conditionals)
- [Determining Active Status](#determining-active-status)
- [Sub Menus](#sub-menus)
`Menu`
- [Define]

#### `Item`
##### Name
A new `Item` is created using the `Menu::item()` method. This method receives the name of the `Item`:
```php
Menu::item('Dashboard')

$item->name // Dashboard
```
The name is passed into the Laravel `__()` lang helper before outputting.
##### URL
The url is defined and retrieved as follows:
```php
Menu::item('Dashboard')->for('/dashboard')

$item->url // /dashboard
```
The `for()` method can also be used to construct Laravel routes:
```php
Menu::item('Dashboard')->for('dashboard.show', $customer)
```
The url is not required for an item to function. By default, all items have their url set to `#0` so you can output all hrefs without fear of any pesky `nulls`...
> #0 is the default as html ids cannot begin with a number. So, you can have a clickable anchor without the page bouncing around everywhere!
##### Icons
A reference to an icon in your app can be defined and retrieved as follows:
```php
Menu::item('Dashboard')->icon('dashboard.svg')

$item->icon
```
You may want to use the awesome [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package which itself uses the awesome [Heroicons](https://heroicons.com/) icon set. They can be defined as follows - perhaps to use with a [dynamic component](https://laravel.com/docs/master/blade#dynamic-components):
```php
Menu::item('Dashboard')->heroicon('o-cog')

$item->heroicon
```
##### Conditionals
You can define conditionals to determine if the given `Item` should be displayed or not:
```php
Menu::item('Billing')->when(auth()->user()->is_subscribed)

Menu::item('Registration')->unless(auth()->user()->is_subscribed)
```
They can also be composed:
```php
Menu::item('Billing')
    ->when($aCheck)
    ->unless($someOtherCheck)
    ->when($yetAnotherCheck)
```
When your menu items are loaded, any falsey `Items` are filtered out by default.
##### Determining Active Status
A basic need of any item menu is to determine if it is active or not. To do so, simply access the `active` property:
```blade
@if ($item->active)
...
@endif
```
By default, an `Item` will return true if the current URL matches its URL.
##### Sub Menus
Creating a sub menu for any given item is simple - just define it as so:
```php
Menu::item('Billing')->subMenu([
    Menu::item('Invoices'),
    ...
])
```
There's no limit to the number of sub menus you can have, and sub menus themselves can have sub menus. It's probably rare that would be needed, but the power is there if needed.

A common need with sub menus is determing if any of the sub menu's `Items` are active, perhaps to expand the drop down list of the parent `Item`. Rather than looping through each decendant and determining if it is [`active`](#determining-active-status) or not, you can call `subActive`:
```blade
@if ($item->subActive)
...
@endif
```
This will return true regardless of nesting - even for grandchildren or great-great-greatgrandchildren. If one of a parent's decendants are active, even though `subActive` will return `true`, `active` will not. This only applies to the `Item` is accessed on.

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
