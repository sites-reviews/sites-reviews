<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReview;
use App\Review;
use App\Site;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreReview $request
     * @param  Site $site
     * @return Response
     */
    public function store(StoreReview $request, Site $site)
    {
        $review = new Review();
        $review->fill($request->all());

        $site->reviews()->save($review);

        return redirect()
            ->route('sites.show', $site)
            ->with(['success' => __('The review was published successfully')]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Review $review
     * @return Response
     * @throws AuthorizationException
     */
    public function show(Review $review)
    {
        $comments = $review->comments()
            ->roots()
            ->with('create_user.avatarPreview', 'authUserRatings')
            ->get();

        return view('site.review.show', [
            'review' => $review,
            'comments' => $comments
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Review $review
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Review $review)
    {
        $this->authorize('edit', $review);

        return view('site.review.edit', ['review' => $review]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Review $review
     * @return Response
     * @throws AuthorizationException
     */
    public function update(StoreReview $request, Review $review)
    {
        $this->authorize('edit', $review);

        $review->fill($request->all());
        $review->save();

        return redirect()
            ->route('sites.show', $review->site)
            ->with(['success' => __('The review was updated successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Review $review
     * @return mixed
     * @throws AuthorizationException
     */
    public function destroy(Request $request, $review)
    {
        $review = Review::withTrashed()
            ->findOrFail($review);

        if ($review->trashed())
        {
            $this->authorize('restore', $review);

            $review->restore();
        }
        else
        {
            $this->authorize('delete', $review);

            $review->delete();
        }

        if ($request->ajax())
        {
            return $review;
        }
        else
        {
            if ($review->trashed())
                return redirect()
                    ->route('sites.show', $review->site)
                    ->with(['success' => __('The review was successfully deleted')]);
            else
                return redirect()
                    ->route('sites.show', $review->site)
                    ->with(['success' => __('The review was successfully restored')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Review $review
     * @return array
     * @throws AuthorizationException
     */
    public function rateUp(Request $request, Review $review)
    {
        $this->authorize('rateUp', $review);

        $rating = $review->rate(Auth::user(), 1);

        $review->refresh();

        if ($request->ajax())
        {
            return [
                'rateable' => ['rating' => $review->rating],
                'rating' => $rating
            ];
        }
        else
        {
            return redirect()
                ->route($review->getRedirectToUrl());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Review $review
     * @return array
     * @throws AuthorizationException
     */
    public function rateDown(Request $request, Review $review)
    {
        $this->authorize('rateDown', $review);

        $rating = $review->rate(Auth::user(), -1);

        $review->refresh();

        if ($request->ajax())
        {
            return [
                'rateable' => ['rating' => $review->rating],
                'rating' => $rating
            ];
        }
        else
        {
            return redirect()
                ->route($review->getRedirectToUrl());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Review $review
     * @return array
     * @throws AuthorizationException
     */
    public function goTo(Review $review)
    {
        return redirect()
            ->route('sites.show', $review->site);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function comments(Review $review)
    {
        $comments = $review->comments()
            ->roots()
            ->oldest()
            ->with('create_user.avatarPreview', 'authUserRatings')
            ->get();

        $view = view('site.review.comment.index', [
            'review' => $review,
            'comments' => $comments
        ]);

        if (request()->ajax())
            return $view->renderSections()['content'];
        else
            return $view;
    }
}
