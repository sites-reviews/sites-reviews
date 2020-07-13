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
            ->with('create_user', 'site', 'authUserRatings')
            ->simplePaginate();

        return view('home', ['reviews' => $reviews]);
    }
}
