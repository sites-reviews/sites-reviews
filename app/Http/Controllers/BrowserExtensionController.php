<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Litlife\Url\Url;
use GuzzleHttp\Client;

class BrowserExtensionController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param Request $request
     */
    public function redirect(Request $request)
    {
        $url = Url::fromString($request->url);

        $host = $url->getHost();

        if (!empty($host))
        {
            return redirect()
                ->route('sites.create.or_show', ['domain' => $host]);
        }
        else
        {
            return redirect()
                ->route('sites.search', ['term' => (string)$url]);
        }
    }
}
