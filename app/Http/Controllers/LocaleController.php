<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class LocaleController extends Controller
{
    public function setLocale(URL $url, string $locale)
    {
        $previousRequest = Request::create(url()->previous());

        $previousRoute = Route::getRoutes()->match($previousRequest);

        $parameters = array_merge($previousRoute->parameters(), $previousRequest->all());

        if ($previousRoute->uri() != '{fallbackPlaceholder}' and in_array($locale, \Config::get('app.locales'))) {

            cookie('locale', $locale);

            if (Auth::check()) {
                $user = Auth::user();
                $user->selected_locale = $locale;
                $user->save();
            }

            $parameters['locale'] = $locale;

            return redirect()
                ->route($previousRoute->getName(), $parameters)
                ->cookie('locale', $locale, (60 * 24 * 31));
        } else {
            return redirect()
                ->route('home');
        }
    }

    public function dropdownList()
    {
        $localeArray = config('app.local_flag_map');

        return view('other.select_language', ['locale' => $localeArray]);
    }
}
