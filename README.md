# Navigator

![Tests](https://github.com/nedwors/navigator/workflows/Tests/badge.svg)

Navigator is a package to create headless navigation menus for use in Laravel applications:

```php
// In a Service Provider

Nav::define(fn ($user) => [
    Nav::item('Dashboard')
        ->for('dashboard')
        ->icon('dashboard.svg')
        ->when($user->can('access.dashboard'))
])

// In a view

@foreach(Nav::items() as $item)
    //
@endforeach
```
> This is a headless package so you are completely free to style as you see fit.

## Installation

You can install the package via composer:

```bash
composer require nedwors/navigator
```

## Usage

Select a Service Provider - or perhaps make a dedicated one - and pop the following in:
```php
Nav::define(fn () => [
    Nav::item('Dashboard')
        ->for('dashboard')
        ->icon('dashboard')
]);
```

Et voila! You now have a menu ready to use. You can retrieve the items as follows:

```blade
@foreach(Nav::items())
    //
@endforeach

// or

@foreach(navitems())
    //
@endforeach
```
Now, you'll probably want more than just a dashboard link - then again maybe not, it's your app! - but hopefully you can see how easy it is to get up and running.

Let's dig further into the features available to you:

### API
The package consists of two main features - each `Nav` and the `Items` within. We will explore the `Item` and then the `Nav`.

It's worth noting at this point that `Item` extends `Illuminate\Support\Fluent` to allow for custom methods/properties on a per project basis. Also, `Nav` is macroable to allow for custom functionality in your projects.

`Item`
- [Name](#name)
- [URL](#url)
- [Icons](#icons)
- [Conditionals](#conditionals)
- [Determining Active Status](#determining-active-status)
- [Sub Items](#sub-items)
`Nav`
- [Define](#define)
- [Filter](#filter)
- [Active When](#active-when)

#### `Item`
##### Name
A new `Item` is created using the `Nav::item()` method. This method receives the name of the `Item`:
```php
Nav::item('Dashboard')

$item->name // Dashboard
```
> The name is passed into the Laravel `__()` lang helper before outputting.
##### URL
The url is defined and retrieved as follows:
```php
Nav::item('Dashboard')->for('/dashboard')

$item->url // /dashboard
```
The `for()` method can also be used to construct Laravel routes:
```php
Nav::item('Dashboard')->for('dashboard.show', $customer)
```
The url is not required for an item to function. By default, all items have their url set to `#0` so you can output all hrefs without fear of any pesky `nulls`...
> #0 is the default as html ids cannot begin with a number. So, you can have a clickable anchor without the page bouncing around everywhere...
##### Icons
A reference to an icon in your app can be defined and retrieved as follows:
```php
Nav::item('Dashboard')->icon('dashboard.svg')

$item->icon
```
You may want to use the awesome [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package which itself uses the awesome [Heroicons](https://heroicons.com/) icon set. They can be defined as follows - perhaps to use with a [dynamic component](https://laravel.com/docs/master/blade#dynamic-components):
```php
Nav::item('Dashboard')->heroicon('o-cog')

$item->heroicon
```
##### Conditionals
You can define conditionals to determine if the given `Item` should be displayed or not:
```php
Nav::item('Billing')->when(auth()->user()->is_subscribed)

Nav::item('Registration')->unless(auth()->user()->is_subscribed)
```
They can also be composed:
```php
Nav::item('Billing')
    ->when($aCheck)
    ->unless($someOtherCheck)
    ->when($yetAnotherCheck)
```
When your nav items are loaded, any falsey `Items` are filtered out by default.

> This behaviour can be [modified if desired](#filter)
##### Determining Active Status
A basic need of any menu item is to determine if it is active or not. To do so, simply access the `active` property:
```blade
@if ($item->active)
...
@endif
```
> By default, an `Item` will return true if the current URL matches its URL. You can [configure this behaviour](#active-when)
##### Sub Items
Creating sub items for any given item is simple - just define as so:
```php
Nav::item('Billing')->subItems([
    Nav::item('Invoices'),
    ...
])
```
There's no limit to the number of sub menus you can have, and sub menus themselves can have sub menus. It's probably rare that would be needed, but the power is there if needed. Also,
generators can be passed as the sub menu:
```php
Nav::item('Billing')->subItems(fn () => yield from [
    // Sub Items here...
])
```
> To learn more, see the [`define`](#define) section.

A common need with sub menus is determing if any of the sub items are active, perhaps to expand the drop down list of the parent `Item`. Rather than looping through each decendant and determining if it is [`active`](#determining-active-status) or not, you can call `hasActiveDecendants`:
```blade
@if ($item->hasActiveDecendants)
...
@endif
```
This will return true regardless of nesting - even for grandchildren or great-great-greatgrandchildren (you get the idea). If one of a parent's decendants are active, even though `hasActiveDecendants` will return `true`, `active` will not. This only applies to the `Item` is accessed on.

#### Nav

Now we've seen how to make some `Items`, we need to actually make a menu! At its simplest, we can define a menu and retrieve a menu. But we also have control over advanced functions
such as filtering. Let's start by making a menu:

#### Define
To create a menu, use the `define` method:
```php
Nav::define(fn () => [
    // Items go here...
]);
```
As you can see, the `define` method should be passed a closure that returns an `iterable`. Under the hood, the `Items` are held as a `LazyCollection` to aid performance. As such, a generator can be returned instead of a plain array:
```php
Nav::define(fn () => yield from [
    // Items go here...
]);
```
This probably won't be needed on most projects, but the power is there if needed.

The closure that you pass to define receives both `auth()->user()` and `app()` for convenience - think for [`conditionals`](#conditionals):
```php
Nav::define(fn (?Authenticable $user, Application $app) => [
    // Items go here...
]);
```
How about multiple menus? No problem, just pass the menu name as the second argument to each menu definition:
```php
Nav::define(fn () => [
    // Items go here...
], 'admin');
```
#### Items
Now we've defined the menus, we need to output them in our views! This can be acheived by:
```php
Nav::items()

// or

navitems()
```
Both these return a `LazyCollection` of the menu `Items`. If you need access to a specific menu, this can be passed as an argument:
```php
Nav::items('admin')

// or

navitems('admin')
```
#### Filter
All those [`conditionals`](#conditionals) you set up need to do something right? Well, by default all `Items` that are not truthy because of their
conditionals will be filtered out. If you would like to control what should be filtered out, use the `filter` method. This method accepts a `Closure`
with the same structure as a `Collection` filter:
```php
Nav::filter(fn (Item $item) => ...)
```
You can also define filters for multiple menus:
```php
Nav::filter(fn (Item $item) => ..., 'app')
Nav::filter(fn (Item $item) => ..., 'admin')
```
> All `filters` are applied to all sub items of the given menu too.

You probably won't need to use this functionality, but it's there if needed.
#### Active When
By default, `Items` active property will be true when the current url is the `Item's` url. This applies whether you defined the `Item` using a named route
or a url. If you would like to override what constitutes an `Item` as being active, you cna use the `activeWhen` method. This should be passed a `Closure` that
receives an `Item`:
```php
Nav::activeWhen(fn (Item $item) => ...)
```
This may be useful for use cases outside the realms of traditional routing.
Like [`filter'](#filter), this can be defined per menu:
```php
Nav::activeWhen(fn (Item $item) => ..., 'app')
Nav::activeWhen(fn (Item $item) => ..., 'admin')
```
> The active check will be used for all sub menus within the menu too.
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
