<?php

namespace App\Http\Controllers;

use App\Review;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $reviews = Review::latest()
            ->accepted()
            ->with('create_user', 'site.siteOwners', 'authUserRatings')
            ->simplePaginate();

        return view('home', ['reviews' => $reviews, 'dont_show_header_search' => true]);
    }
}
