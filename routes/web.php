<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/sitemap.xml', 'OtherController@sitemapRedirect')->name('sitemap');

$sitePregPattern = '([A-Za-z0-9\-\.]+)\.([A-z]+)';

Route::prefix('{locale}')
    ->where(['locale' => '[a-zA-Z]{2}'])
    ->group(function () use ($sitePregPattern) {

        Route::get('/', 'HomeController@index')->name('home');
        Route::get('/sites', 'SiteController@index')->name('sites.index');
        Route::get('/{site}', 'SiteController@show')->name('sites.show')->where('site', $sitePregPattern);
        Route::get('/sites/{site}/edit', 'SiteController@edit')->name('sites.edit');

        Route::get('/sites/{site}/ratings/create', 'SiteController@rating')->name('sites.ratings.create');

        Route::get('/users/{user}', 'UserController@show')->name('users.show');
        Route::get('/reviews/{review}/go_to', 'ReviewController@goTo')->name('reviews.go_to');
        Route::get('/comments/{comment}/go_to', 'CommentController@goTo')->name('comments.go_to');

        Route::post('/reviews/{review}/comments', 'CommentController@store')->name('reviews.comments.store');

        Route::get('/sites/{domain}/create/or_show', 'SiteController@createOrShow')->name('sites.create.or_show');

        Route::get('/reviews/{review}/comments', 'ReviewController@comments')->name('reviews.comments');
        Route::get('/comments/{comment}/children', 'CommentController@children')->name('comments.children');
        Route::get('/comments/{comment}', 'CommentController@show')->name('comments.show');
        Route::get('/reviews/{review}', 'ReviewController@show')->name('reviews.show');

        Route::get('/users/{user}/avatar', 'UserController@avatarShow')->name('users.avatar');
        Route::get('/test', 'OtherController@test')->name('test');
        Route::get('/phpinfo', 'OtherController@phpinfo')->name('phpinfo');

        Route::group(['middleware' => ['guest']], function () {
            Route::get('/invitations', 'UserInvitationController@create')->name('users.invitation.create');
            Route::post('/invitations', 'UserInvitationController@store')->name('users.invitation.store');

            Route::get('/invitations/user/create/{token}', 'UserInvitationController@createUser')->name('users.invitation.create.user');
            Route::post('/invitations/user/store', 'UserInvitationController@storeUser')->name('users.invitation.store.user');

            Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

            Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
            Route::post('login', 'Auth\LoginController@login');
        });

        Route::group(['middleware' => ['auth']], function () {

            Route::post('logout', 'Auth\LoginController@logout')->name('logout');

            Route::post('/sites/{site}/review', 'ReviewController@store')->name('reviews.store');
            Route::get('/reviews/{review}/edit', 'ReviewController@edit')->name('reviews.edit');
            Route::patch('/reviews/{review}', 'ReviewController@update')->name('reviews.update');
            Route::delete('/reviews/{review}', 'ReviewController@destroy')->name('reviews.destroy');

            Route::get('/users/{user}/reviews', 'UserController@reviews')->name('users.reviews');
            Route::get('/users/{user}/settings', 'UserController@settings')->name('users.settings');

            Route::get('/reviews/{review}/rate/up', 'ReviewController@rateUp')->name('reviews.rate.up');
            Route::get('/reviews/{review}/rate/down', 'ReviewController@rateDown')->name('reviews.rate.down');

            Route::get('/sites/{site}/verification', 'SiteOwnerController@request')->name('sites.verification.request');
            Route::get('/sites/{site}/verification/check/dns', 'SiteOwnerController@checkDns')->name('sites.verification.check.dns');
            Route::get('/sites/{site}/verification/check/file', 'SiteOwnerController@checkFile')->name('sites.verification.check.file');
            Route::get('/sites/{site}/verification/check/file/download', 'SiteOwnerController@downloadFile')->name('sites.verification.check.file.download');

            Route::get('/users/{user}/settings', 'UserSettingController@settings')->name('users.settings');
            Route::post('/users/{user}/avatar', 'UserSettingController@storeAvatar')->name('users.avatar.store');

            Route::get('/users/{user}/settings/notifications', 'UserSettingController@notifications')->name('users.settings.notifications');
            Route::post('/users/{user}/settings/notifications', 'UserSettingController@notificationsUpdate')->name('users.settings.notifications.update');

            Route::patch('/users/{user}', 'UserController@update')->name('users.update');

            Route::get('/preview_notification', 'OtherController@previewNotification')->name('preview_notification');

            Route::get('/users/{user}/notifications/dropdown', 'UserController@notificationsDropdown')->name('users.notifications.dropdown');

            Route::get('/reviews/{review}/comments/create', 'CommentController@create')->name('reviews.comments.create');
            Route::post('/reviews/{review}/comments', 'CommentController@store')->name('reviews.comments.store');

            Route::get('/comments/{comment}/rate/up', 'CommentController@rateUp')->name('comments.rate.up');
            Route::get('/comments/{comment}/rate/down', 'CommentController@rateDown')->name('comments.rate.down');
            Route::delete('/comments/{comment}', 'CommentController@destroy')->name('comments.destroy');
            Route::get('/comments/{comment}/edit', 'CommentController@edit')->name('comments.edit');
            Route::patch('/comments/{comment}', 'CommentController@update')->name('comments.update');

            Route::get('/comments/{comment}/replies/create', 'CommentController@replyCreate')->name('comments.replies.create');
            Route::post('/comments/{comment}/replies', 'CommentController@replyStore')->name('comments.replies.store');

            Route::get('/comments/{comment}/edit', 'CommentController@edit')->name('comments.edit');
            Route::patch('/comments/{comment}', 'CommentController@update')->name('comments.update');

            Route::delete('/comments/{comment}', 'CommentController@destroy')->name('comments.destroy');

            Route::get('/users/{user}/notifications', 'UserController@notifications')->name('users.notifications');
        });

        Route::get('/ratings_colors', 'SiteController@ratingsColors')->name('ratings.colors');

        Route::get('/sites_search', 'SiteController@search')->name('sites.search');

        Route::post('/sites/{site}/review', 'ReviewController@store')->name('reviews.store');

        Route::get('/personal_data_processing_agreement', 'OtherController@personalDataProcessingAgreement')->name('personal_data_processing_agreement');

        Route::get('/auth/{provider}', 'UserSocialAccountController@redirectToProvider')
            ->name('social_accounts.redirect')
            ->where('provider', '(google|facebook|vkontakte)');

        Route::get('/auth/{provider}/callback', 'UserSocialAccountController@handleProviderCallback')
            ->name('social_accounts.callback')
            ->where('provider', '(google|facebook|vkontakte)');
    });

Route::get('/sites_rating/{size}/{site}.png', 'SiteController@ratingImage')->name('sites.rating.image')
    ->where('site', $sitePregPattern)
    ->where('size', '(1x|2x|3x)')
    ->withoutMiddleware(\Illuminate\View\Middleware\ShareErrorsFromSession::class)
    ->withoutMiddleware(\Illuminate\Session\Middleware\StartSession::class)
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
    ->withoutMiddleware(\App\Http\Middleware\SetLocale::class);

Route::get('locale/list', 'LocaleController@dropdownList')->name('locale.list');
Route::get('locale/set/{set_locale}', 'LocaleController@setLocale')->name('locale.set');

Route::fallback('OtherController@error404')
    ->name('error.404')
    ->withoutMiddleware(\App\Http\Middleware\SetLocale::class);

