<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\SiteResource;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$sitePregPattern = '([A-Za-z0-9\-\.]+)\.([A-z]+)';

Route::get('/sites/{site}', 'Api\SiteController@show')->name('api.sites.show')->where('site', $sitePregPattern);
Route::get('/sites', 'Api\SiteController@index')->name('api.sites.index');
