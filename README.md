# Laravel Nova Publishable

[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-publishable.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-publishable)
[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)

## Introduction 

This package allows you to manage Laravel Models which user [Laravel Publishable](https://github.com/novius/laravel-publishable) in [Laravel Nova](https://nova.laravel.com/).  

## Requirements

* Laravel Nova >= 4.0
* Laravel >= 10.0
* PHP >= 8.2

> **NOTE**: These instructions are for Laravel >= 10.0 and PHP >= 8.2 If you are using prior version, please
> see the [previous version's docs](https://github.com/novius/laravel-publishable/tree/2.x).

## Installation

You can install the package via composer:

```bash
composer require novius/laravel-nova-publishable
```

Add `Publishable` trait on your Nova Resource:

```php
use Laravel\Nova\Resource;
use Novius\LaravelNovaPublishable\Nova\Traits\Publishable;

class Post extends Resource
{
    use Publishable;
```

Then you can insert Publishable fields on your Nova Resource.
You can also add the Publication Status Filter.

```php
class Post extends Resource
{
    public function fields(NovaRequest $request): array
    {
        return [
            PublicationBadge::make(), // Only display on not forms
            PublicationStatusField::make()->onlyOnForms(),
            PublishedFirstAt::make()->hideFromIndex(),
            PublishedAt::make()->onlyOnForms(),
            ExpiredAt::make()->onlyOnForms(),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new PublicationStatus(),
        ];
    }
```

You can use the UpdatePlucationStatus action to mass update the publication status of your models.

```php  
    public function actions(Request $request): array
    {
        return [
            UpdatePublicationStatus::make(),
        ];
    }
```
## Lang files

If you want to customize the lang files, you can publish them with:

```bash
php artisan vendor:publish --provider="Novius\LaravelNovaPublishable\LaravelNovaPublishableServiceProvider" --tag="lang"
```

## Lint

Lint your code with Laravel Pint using:

```bash
composer run-script lint
```

## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
