<?php

namespace App\Http\Middleware;

use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\TwitterCard;
use Closure;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Litlife\Url\Url;

class SEOMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        SEOTools::setDescription(__('Read and write reviews about websites and companies.').' '.
            __('Expert reviews, user opinions, and comments from site clients'));

        SEOMeta::addKeyword(implode(', ', [
            __('site rating'),
            __('feedback about the website'),
            __('website'),
            __('discussion'),
            __('opinion'),
            __('communication'),
            __('rating'),
        ]));

        $url = Url::fromString($request->fullUrl());

        $page = intval($url->getQueryParameter('page'));

        if ($page < 2) {
            $url = (string)$url->withoutQueryParameter('page');
        } elseif ($page > 1) {
            $url = (string)$url->withQueryParameter('page', $page);
        } else {
            $url = (string)$url;
        }

        $title = ltrim(Breadcrumbs::pageTitle(), ' ');

        SEOTools::opengraph()->setUrl($url);
        SEOTools::setCanonical($url);
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::jsonLd()->addImage(Url::fromString(config('app.url') . '/img/brand.png'));

        TwitterCard::setUrl($url);
        SEOMeta::setCanonical($url);
        OpenGraph::addProperty('locale', config('app.locale').'-'.config('app.locale'));

        return $next($request);
    }

    public function terminate($request, $response)
    {
        Facade::clearResolvedInstance('seotools.opengraph');
        Facade::clearResolvedInstance('seotools.metatags');
        Facade::clearResolvedInstance('seotools');
        Facade::clearResolvedInstance('seotools.twitter');
    }
}
