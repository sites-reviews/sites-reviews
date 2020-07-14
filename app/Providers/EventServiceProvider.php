<?php

namespace App\Providers;

use App\Listeners\SendPasswordChangeIsSuccessfulNotification;
use App\Listeners\SendWelcomeNotification;
use App\Listeners\TryRestoreSavedLocale;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendWelcomeNotification::class
        ],
        PasswordReset::class => [
            SendPasswordChangeIsSuccessfulNotification::class
        ],
        Login::class => [
            TryRestoreSavedLocale::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
