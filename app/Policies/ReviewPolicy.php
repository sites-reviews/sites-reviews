<?php

namespace App\Policies;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy extends Policy
{
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function show(User $authUser, Review $review)
    {
        return $this->allow();
    }

    public function create(User $authUser, Review $review)
    {
        return $this->allow();
    }

    public function reply(User $authUser, Review $review)
    {
        if ($review->isUserCreator($authUser))
            return $this->deny(__("You can't respond to your review"));
        else
            return $this->allow();
    }

    public function edit(User $authUser, Review $review)
    {
        if ($review->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__('review.you_dont_have_the_right_to_edit_this_review'));
    }

    public function delete(User $authUser, Review $review)
    {
        if ($review->trashed())
            return $this->deny(__('review.review_was_already_deleted'));

        if ($review->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__('review.you_dont_have_the_right_to_delete_this_review'));
    }

    public function restore(User $authUser, Review $review)
    {
        if (!$review->trashed())
            return $this->deny(__('review.review_was_not_deleted'));

        if ($review->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__('review.you_dont_have_the_right_to_restore_this_review'));
    }

    public function rateUp(User $authUser, Review $review)
    {
        if ($review->isUserCreator($authUser))
            return $this->deny(__('review.you_cant_rate_your_reviews'));
        else
            return $this->allow();
    }

    public function rateDown(User $authUser, Review $review)
    {
        if ($review->isUserCreator($authUser))
            return $this->deny(__('review.you_cant_rate_your_reviews'));
        else
            return $this->allow();
    }
}
