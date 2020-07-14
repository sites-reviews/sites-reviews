<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Config;
use Session;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //$rawLocale = Session::get('locale');
        $rawLocale = $request->session()->get('locale');

        if (empty($rawLocale))
        {
            if ($request->hasCookie('locale'))
            {
                $request->session()
                    ->put(['locale' => $request->cookie('locale')]);
            }
        }

        if (in_array($rawLocale, Config::get('app.locales'))) {
            $locale = $rawLocale;
        } else {
            $locale = Config::get('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
