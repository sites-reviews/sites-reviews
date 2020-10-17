<?php

namespace App\Providers;

use App\Enums\Gender;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {

            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(DuskBrowserServiceProvider::class);
        }

        if (app()->runningInConsole()) {

            $argv = Request::server('argv', null);

            if ($argv[0] == 'artisan' && Str::contains($argv[1], 'migrate')) {

                $platform = DB::getDoctrineSchemaManager()
                    ->getDatabasePlatform();

                $platform->registerDoctrineTypeMapping('gender', 'string');
                $platform->registerDoctrineTypeMapping('storages', 'string');
                $platform->registerDoctrineTypeMapping('morph', 'string');
                $platform->registerDoctrineTypeMapping('read_statuses', 'string');
            }
        }

        Carbon::setLocale(config('app.locale'));
        Date::setLocale(config('app.locale'));

        setlocale(LC_TIME, config('app.locale') . '_' . mb_strtoupper(config('app.locale')) . '.UTF-8');

        URL::defaults(['locale' => config('app.locale')]);

        Paginator::useBootstrap();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        \App\Image::observe(\App\Observers\ImageObserver::class);
        \App\Review::observe(\App\Observers\ReviewObserver::class);
        \App\ProofOwnership::observe(\App\Observers\ProofOwnershipObserver::class);
        \App\ReviewRating::observe(\App\Observers\ReviewRatingObserver::class);
        \App\Comment::observe(\App\Observers\CommentObserver::class);
        \App\CommentRating::observe(\App\Observers\CommentRatingObserver::class);
        \App\UserInvitation::observe(\App\Observers\UserInvitationObserver::class);
        \App\PasswordReset::observe(\App\Observers\PasswordResetObserver::class);
        \App\Site::observe(\App\Observers\SiteObserver::class);

        Validator::extend('gender', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, Gender::getKeys());
        });
    }
}
