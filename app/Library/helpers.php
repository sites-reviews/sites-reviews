<?php

/*
|--------------------------------------------------------------------------
| Detect Active Route
|--------------------------------------------------------------------------
|
| Compare given route with current route and return output if they match.
| Very useful for navigation, marking if the link is active.
|
*/
function isActiveRoute($routes, $output = "active")
{
    if (is_array($routes)) {
        if (in_array(Route::currentRouteName(), $routes))
            return $output;
    } else {
        if (Route::currentRouteName() == $routes)
            return $output;
    }
}

/*
|--------------------------------------------------------------------------
| Detect Active Routes
|--------------------------------------------------------------------------
|
| Compare given routes with current route and return output if they match.
| Very useful for navigation, marking if the link is active.
|
*/
function areActiveRoutes(Array $routes, $output = "active")
{
    foreach ($routes as $route) {
        if (Route::currentRouteName() == $route) return $output;
    }

}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($s)
    {
        $s1 = mb_strtoupper(mb_substr($s, 0, 1));
        $s2 = mb_substr($s, 1);
        return $s1 . '' . $s2;
    }
}

function replaceAsc194toAsc32($s)
{
    mb_substitute_character(0x20);
    $s = mb_convert_encoding($s, "UTF-8", "auto");

    return mb_str_replace(chr(194) . chr(160), ' ', $s);
}

function currentRouteUrlWithParameters(array $array)
{
    $route = \Route::current();

    foreach ($array as $key => $value)
    {
        $route->setParameter($key, $value);
    }

    return route($route->getName(), array_merge($route->parameters(), \Illuminate\Support\Facades\Request::all()));
}
