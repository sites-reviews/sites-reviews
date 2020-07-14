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
            return $this->deny(__('You dont have the right to edit this review'));
    }

    public function delete(User $authUser, Review $review)
    {
        if ($review->trashed())
            return $this->deny(__('Review was already deleted'));

        if ($review->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__('You dont have the right to delete this review'));
    }

    public function restore(User $authUser, Review $review)
    {
        if (!$review->trashed())
            return $this->deny(__('Review was not deleted'));

        if ($review->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__('You dont have the right to restore this review'));
    }

    public function rateUp(User $authUser, Review $review)
    {
        if ($review->isUserCreator($authUser))
            return $this->deny(__('You cant rate your reviews'));
        else
            return $this->allow();
    }

    public function rateDown(User $authUser, Review $review)
    {
        if ($review->isUserCreator($authUser))
            return $this->deny(__('You cant rate your reviews'));
        else
            return $this->allow();
    }
}
