<?php

namespace App\Http\Controllers;

use App\Enums\SiteHowAddedEnum;
use App\Site;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Litlife\Url\Url;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sites = Site::query()
            ->simplePaginate(5);

        return view('site.index', ['sites' => $sites]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Site $site)
    {
        $this->authorize('show', $site);

        $reviews = $site->reviews()
            ->when(auth()->check(), function ($query) {
                $query->where('create_user_id', '!=', Auth::id());
            })
            ->with('create_user', 'authUserRatings');

        if (empty($request->reviews_order_by))
            $request->reviews_order_by = 'latest';

        switch ($request->reviews_order_by) {
            case 'latest':
                $reviews->latest();
                break;
            case 'rating_desc':
                $reviews->orderBy('rating', 'desc');
                break;
        }

        if (auth()->check()) {
            $authReview = $site->reviews()
                ->where('create_user_id', Auth::id())
                ->first();
        }

        if (!empty($site->preview))
        {
            SEOTools::addImages($site->preview->fullUrlMaxSize(200, 200));
        }

        if ($site->isDomainLikeTitle())
            $title = $site->title;
        else
            $title = $site->title.' - '.$site->domain;

        SEOTools::setTitle(__('site.browser_title', ['title' => $title]).' - '.config('app.name'))
            ->setDescription(__('Reviews and rating of a site or company :title', [
                'title' => $title
            ]));

        return view('site.show', [
            'site' => $site,
            'reviews' => $reviews->simplePaginate(),
            'authReview' => $authReview ?? null,
            'reviews_order_by' => $request->reviews_order_by
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        return view('site.edit', ['site' => $site]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        //
    }

    public function ratingImage($size, Site $site)
    {
        if (App::isLocal())
            \Debugbar::startMeasure('render_image','Render image');

        $blob = $site->getRatingImageBlob($size, true);

        if (App::isLocal())
            \Debugbar::stopMeasure('render_image');

        $seconds = 3600;

        return response($blob, 200)
            //->setLastModified(new \DateTime($site->latest_rating_changes_at))
            ->header('Cache-control', 'max-age='.$seconds.', public')
            ->header('Pragma', 'cache')
            ->setTtl($seconds)
            ->setExpires(new \DateTime(now()->addSeconds($seconds)))
            ->header('Content-Type', 'image/png')
            ->header('Content-length', strlen($blob));
    }

    public function ratingsColors()
    {
        return view('rating_colors');
    }

    public function search(Request $request)
    {
        $query = Site::fulltextSearch($request->term);

        $url = trim(filter_var($request->term,FILTER_SANITIZE_STRING));

        preg_match('/(?:https|http)?(?:\:\/\/)?([[:graph:]\-\.]+)/iu', $url, $matches);

        if (!empty($matches[1]))
            $domain = $matches[1];

        if (preg_match('/([[:graph:]\-\.]+)\.([A-z]+)/iu', $url))
            $isDomain = true;
        else
            $isDomain = false;

        if ($isDomain)
        {
            $domain = trim(mb_strtolower(Url::fromString('http://'.$domain)->getHost()));

            if (preg_match('/^(?:www\.?)(.*)$/iu', $domain, $matches)) {
                $domain = $matches[1];
            }

            $query->orWhereDomain($domain);
        }

        $sites = $query->orderBy('number_of_reviews', 'asc')
            ->simplePaginate();

        if ($isDomain)
        {
            $count = $sites->where('domain', $domain)->count();

            if ($count < 1)
            {
                $addSite = true;
            }
            elseif ($count == 1 and $sites->count() == 1)
            {
                return redirect()
                    ->route('sites.show', ['site' => $sites->first()]);
            }
        }
        else
        {
            $addSite = false;
        }

        return view('site.search', [
            'term' => $request->term,
            'sites' => $sites,
            'isDomain' => $isDomain,
            'addSite' => $addSite,
            'domain' => $domain ?? null
        ]);
    }

    public function createOrShow(Request $request, $domain, Client $client)
    {
        $domain = trim($domain);

        $site = Site::whereDomain($domain)->first();

        if (!empty($site))
        {
            return redirect()
                ->route('sites.show', $site)
                ->with('site_exists', true);
        }
        else
        {
            $url = Url::fromString('')
                ->withHost($domain)
                ->withScheme('http');

            $site = new Site();
            $site->domain = $url->getHost();

            try
            {
                $site->isAvailableThroughInternet($client);

            } catch (ClientException $exception) {

                report($exception);

                return redirect()
                    ->route('sites.search', ['term' => $domain])
                    ->withInput()
                    ->withErrors(['error' => __("Error adding a site")], 'create_site');
            } catch (ConnectException $exception) {

                report($exception);

                $context = $exception->getHandlerContext();

                return redirect()
                    ->route('sites.search', ['term' => $domain])
                    ->withInput()
                    ->withErrors(['error' => __("Error adding a site")], 'create_site');

            } catch (\Exception $exception) {

                report($exception);

                return redirect()
                    ->route('sites.search', ['term' => $domain])
                    ->withInput()
                    ->withErrors(['error' => __("Error adding a site")], 'create_site');
            }

            $site->title = $url->getHost();
            $site->update_the_preview = true;
            $site->update_the_page = true;
            $site->how_added = SiteHowAddedEnum::Manually;
            $site->save();

            return redirect()
                ->route('sites.show', $site)
                ->with('site_created', true);
        }
    }
}
