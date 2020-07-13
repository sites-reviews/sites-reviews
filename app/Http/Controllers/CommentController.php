<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComment;
use App\Review;
use App\Comment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Review $review)
    {
        $this->authorize('reply', $review);

        $view = view('site.review.comment.create', ['review' => $review]);

        if (request()->ajax())
            return $view->renderSections()['content'];
        else
            return $view;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function store(StoreComment $request, Review $review)
    {
        $this->authorize('reply', $review);

        $comment = new Comment();
        $comment->fill($request->all());

        $review->comments()->save($comment);

        $comment->refresh();

        if ($request->ajax()) {
            return $comment;
        } else {
            return redirect()
                ->route('comments.go_to', $comment);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function children(Comment $comment)
    {
        $comments = Comment::childs($comment->id)
            ->oldest()
            ->with('create_user.avatarPreview', 'authUserRatings')
            ->get();

        $view = view('site.review.comment.children', [
            'comment' => $comment,
            'comments' => $comments
        ]);

        if (request()->ajax())
            return $view->renderSections()['content'];
        else
            return $view;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        $descendants = Comment::descendants($comment->id)
            ->with('create_user.avatarPreview', 'authUserRatings')
            ->get();

        return view('site.review.comment.show', [
            'comment' => $comment,
            'descendants' => $descendants
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        $this->authorize('edit', $comment);

        return view('site.review.comment.edit', [
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(StoreComment $request, Comment $comment)
    {
        $this->authorize('edit', $comment);

        $comment->fill($request->all());
        $comment->save();

        if ($request->ajax())
            return $comment;
        else
            return redirect($comment->getRedirectToUrl())
                ->with(['success' => __('The comment was edited successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $comment
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function destroy(Request $request, $comment)
    {
        $comment = Comment::withTrashed()
            ->findOrFail($comment);

        if ($comment->trashed()) {
            $this->authorize('restore', $comment);

            $comment->restore();
        } else {
            $this->authorize('delete', $comment);

            $comment->delete();
        }

        if ($request->ajax()) {
            return $comment;
        } else {
            if ($comment->trashed())
                return redirect($comment->getRedirectToUrl())
                    ->with(['success' => __('The comment was successfully deleted')]);
            else
                return redirect($comment->getRedirectToUrl())
                    ->with(['success' => __('The comment was successfully restored')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function goTo(Comment $comment)
    {

    }

    /**
     * RateUp
     *
     * @param Request $request
     * @param \App\Comment $comment
     * @return array
     * @throws AuthorizationException
     */
    public function rateUp(Request $request, Comment $comment)
    {
        $this->authorize('rateUp', $comment);

        $rating = $comment->rate(Auth::user(), 1);

        $comment->refresh();

        if ($request->ajax())
        {
            return [
                'rateable' => ['rating' => $comment->rating],
                'rating' => $rating
            ];
        }
        else
        {
            return redirect()
                ->route($comment->getRedirectToUrl());
        }
    }

    /**
     * RateDown
     *
     * @param Request $request
     * @param \App\Comment $comment
     * @return array
     * @throws AuthorizationException
     */
    public function rateDown(Request $request, Comment $comment)
    {
        $this->authorize('rateDown', $comment);

        $rating = $comment->rate(Auth::user(), -1);

        $comment->refresh();

        if ($request->ajax())
        {
            return [
                'rateable' => ['rating' => $comment->rating],
                'rating' => $rating
            ];
        }
        else
        {
            return redirect()
                ->route($comment->getRedirectToUrl());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function replyCreate(Comment $comment)
    {
        $this->authorize('reply', $comment);

        $view = view('site.review.comment.reply.create', ['comment' => $comment]);

        if (request()->ajax())
            return $view->renderSections()['content'];
        else
            return $view;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function replyStore(StoreComment $request, Comment $comment)
    {
        $this->authorize('reply', $comment);

        $review = $comment->review;

        $replyComment = new Comment();
        $replyComment->parent = $comment;
        $replyComment->fill($request->all());
        $review->comments()->save($replyComment);

        $replyComment->refresh();

        if ($request->ajax()) {
            return $replyComment;
        } else {
            return redirect($replyComment->getRedirectToUrl());
        }
    }
}
