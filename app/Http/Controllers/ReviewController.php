<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReview;
use App\Notifications\ConfirmationOfCreatingReviewNotification;
use App\Review;
use App\Site;
use App\TempReview;
use App\User;
use App\UserInvitation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class ReviewController extends Controller
{
    /**
     * Форма оценкт сайт без входа на сайт
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function create(Site $site)
    {
        if (Auth::check()) {
            $review = $site->reviews()
                ->where('create_user_id', Auth::id())
                ->first();

            return redirect()->route('reviews.edit', ['review' => $review]);
        }

        return view('site.review.create', ['site' => $site]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreReview $request
     * @param Site $site
     * @return Response
     */
    public function store(StoreReview $request, Site $site)
    {
        if (!auth()->check()) {

            $this->validateWithBag('store_review', $request, [
                'email' => 'email|required'
            ], [], __('review'));

            $review = new TempReview($request->all());
            $review->token = Str::random(20);
            $site->tempReviews()->save($review);

            Notification::route('mail', $review->email)
                ->notify(new ConfirmationOfCreatingReviewNotification($review));

            return redirect()
                ->route('reviews.show.temp', ['uuid' => $review->uuid]);
        } else {
            $review = new Review();
            $review->fill($request->all());
            $review->create_user()->associate(auth()->user());
            $site->reviews()->save($review);

            return redirect()
                ->to(route('sites.show', ['site' => $site]).'#'.$review->getAnchorName())
                ->with(['success' => __('The review was published successfully')]);
        }
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

        if ($review->trashed()) {
            $this->authorize('restore', $review);

            $review->restore();
        } else {
            $this->authorize('delete', $review);

            $review->delete();
        }

        if ($request->ajax()) {
            return $review;
        } else {
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

        if ($request->ajax()) {
            return [
                'rateable' => ['rating' => $review->rating],
                'rating' => $rating
            ];
        } else {
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

        if ($request->ajax()) {
            return [
                'rateable' => ['rating' => $review->rating],
                'rating' => $rating
            ];
        } else {
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
            ->to(route('sites.show', ['site' => $review->site]).'#'.$review->getAnchorName());
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

        foreach ($comments as $comment)
            $comment->setRelation('review', $review);

        $view = view('site.review.comment.index', [
            'review' => $review,
            'comments' => $comments
        ]);

        if (request()->ajax())
            return $view->renderSections()['content'];
        else
            return $view;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Review $review
     * @param string $token
     * @return Response
     */
    public function confirm($uuid, string $token)
    {
        $validator = \Illuminate\Support\Facades\Validator::make(['uuid' => $uuid], ['uuid' => 'uuid|required']);

        if (!$validator->valid())
            abort(404);

        $tempReview = TempReview::where('uuid', $validator->valid()['uuid'])
            ->firstOrFail();

        if ($tempReview->token != $token)
            abort(404, __('The link is incorrect or outdated'));

        $user = User::whereEmail($tempReview->email)
            ->verified()
            ->first();

        if (!$user)
        {
            $user = new User();
            $user->email_verified_at = now();
            $user->email = $tempReview->email;
            $user->name = Str::before($tempReview->email, '@');
            $user->password = Str::random();
            $user->save();

            event(new Registered($user));
        }

        $site = $tempReview->site;

        $review = new Review();
        $review->fill($tempReview->toArray());
        $review->create_user()->associate($user);

        if ($site->reviews()->where('create_user_id', $user->id)->accepted()->first())
        {
            $review->statusPrivate();
        }

        $tempReview->site->reviews()->save($review);

        $tempReview->delete();

        Auth::login($user, true);

        if ($review->isAccepted())
            return redirect()
                ->to(route('sites.show', ['site' => $site]).'#'.$review->getAnchorName())
                ->with(['success' => __('A review is successfully published')]);
        else
        {
            return redirect()
                ->to(route('sites.show', ['site' => $site]).'#'.$review->getAnchorName())
                ->with(['success' => __('You already have a review for this site. Your new review is saved as a draft')]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param $review
     * @return Response
     */
    public function showTemp($uuid)
    {
        $validator = \Illuminate\Support\Facades\Validator::make(['uuid' => $uuid], [
            'uuid' => 'uuid|required'
        ]);

        if (!$validator->valid())
            abort(404);

        $review = TempReview::where('uuid', $validator->valid()['uuid'])
            ->firstOrFail();

        return view('site.review.show_temp', ['review' => $review]);
    }

    /**
     * Publishing a review
     *
     * @param Request $request
     * @param Review $review
     * @return mixed
     * @throws AuthorizationException
     */
    public function publish(Request $request, Review $review)
    {
        $this->authorize('publish', $review);

        $review->statusAccepted();
        $review->push();

        $review->site->reviews()
            ->where('create_user_id', $review->create_user_id)
            ->where('id', '!=', $review->id)
            ->each(function (Review $review) {
               $review->statusPrivate();
               $review->save();
            });

        $review->create_user->updateNumberOfReviews();
        $review->create_user->updateNumberOfDraftReviews();
        $review->site->updateRating();
        $review->site->updateNumberOfReviews();
        $review->push();

        if ($request->ajax())
            return $review;
        else
            return redirect()
                ->to($review->getGoToUrl())
                ->with(['success' => __('A review is successfully published')]);
    }
}
