<?php

namespace App\Http\Controllers;

use App\Image;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LocaleController extends Controller
{
    public function setLocale(string $locale)
    {
        $response = redirect()->back();

        if (in_array($locale, \Config::get('app.locales')))
        {
            session(['locale' => $locale]);

            cookie('locale', $locale);

            if (Auth::check())
            {
                $user = Auth::user();
                $user->selected_locale = $locale;
                $user->save();
            }

            return $response->cookie('locale', $locale, (60 * 24 * 31));
        }

        return $response;
    }

    public function dropdownList()
    {
        $localeArray = config('app.local_flag_map');

        return view('other.select_language', ['locale' => $localeArray]);
    }
}
