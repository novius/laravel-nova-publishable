# Laravel Nova Publishable

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)

## Introduction 

This package allows you to manage Laravel Models which user [Laravel Publishable](https://github.com/novius/laravel-publishable) in [Laravel Nova](https://nova.laravel.com/).  

## Requirements

* Laravel Nova >= 4.0
* Laravel Publishable >= 0.1
* Laravel >= 8.0

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

Then you can insert Publishable fields using this method:

```php
    protected function fieldsForIndex(): array
    {
        return [
            ...$this->publishableFields(),
        ];
    }
```

If you want to separate field insertions for Forms and Display:

```php
    protected function fields(): array
    {
        return [
            // Some others fields
            
            ...$this->publishableDisplayFields(),

            // Some others fields
            
            ...$this->publishableFormFields(),
        ];
    }
```

You can also add the Publication Status Filter:

```php
use Laravel\Nova\Resource;
use Novius\LaravelNovaPublishable\Nova\Filters\PublicationStatus;

class Post extends Resource
{
    public function filters(NovaRequest $request): array
    {
        return [
            new PublicationStatus(),
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
