<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SiteResource;
use App\Site;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class SiteController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $sites = (array)$request->sites;

        return SiteResource::collection(\App\Site::whereIn('domain', $sites)
            ->simplePaginate());
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request)
    {
        return SiteResource::collection(\App\Site::whereDomain($request->site)->get());
    }
}
