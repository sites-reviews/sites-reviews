<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    private $locale;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $rawLocale = $request->segment(1);

        if (in_array($rawLocale, Config::get('app.locales')))
            $locale = $rawLocale;

        if (empty($locale)) {
            if ($request->hasCookie('locale')) {
                if (in_array($rawLocale, Config::get('app.locales'))) {
                    $locale = $rawLocale;
                }
            }
        }

        if (empty($locale)) {
            $locale = Config::get('app.locale');
        }

        app()->setLocale($locale);

        URL::defaults(['locale' => $locale]);

        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
