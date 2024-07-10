<?php

namespace Novius\LaravelNovaPublishable;

use Illuminate\Support\ServiceProvider;

class LaravelNovaPublishableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-nova-publishable');

        $this->publishes([
            __DIR__.'/../lang' => lang_path('vendor/laravel-nova-publishable'),
        ], 'lang');
    }
}
