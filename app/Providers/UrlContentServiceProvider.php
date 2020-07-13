<?php

namespace App\Providers;

use App\Service\DNS;
use App\Service\UrlContent;
use Illuminate\Support\ServiceProvider;

class UrlContentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UrlContent::class, function ($app) {
            return new UrlContent;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [UrlContent::class];
    }
}
