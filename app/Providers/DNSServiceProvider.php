<?php

namespace App\Providers;

use App\Service\DNS;
use Illuminate\Support\ServiceProvider;

class DNSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DNS::class, function ($app) {
            return new DNS;
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
        return [DNS::class];
    }
}
