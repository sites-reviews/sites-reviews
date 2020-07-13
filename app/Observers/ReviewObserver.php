<?php

namespace App\Observers;

use App\Review;
use App\Site;
use App\User;

class ReviewObserver
{
    public function creating(Review $review)
    {
        $review->autoAssociateAuthUser();
    }

	/**
	 * Listen to the User created event.
	 *
	 * @param Review $review
	 * @return void
	 */
	public function created(Review $review)
	{
        $this->updateUserNumberOfReviews($review->create_user);
        $this->updateSiteNumberOfReviews($review->site);
        $this->updateRating($review->site);
	}

    public function updated(Review $review)
    {
        if ($review->wasChanged('rate'))
        {
            $this->updateRating($review->site);
        }
    }

	public function deleted(Review $review)
	{
        $this->updateUserNumberOfReviews($review->create_user);
        $this->updateSiteNumberOfReviews($review->site);
        $this->updateRating($review->site);
	}

	public function restored(Review $review)
	{
        $this->updateUserNumberOfReviews($review->create_user);
        $this->updateSiteNumberOfReviews($review->site);
        $this->updateRating($review->site);
	}

	public function updateUserNumberOfReviews(User $user)
    {
        $user->updateNumberOfReviews();
        $user->save();
    }

    public function updateSiteNumberOfReviews(Site $site)
    {
        $site->updateNumberOfReviews();
        $site->save();
    }

    public function updateRating(Site $site)
    {
        $site->updateRating();
        $site->save();

        $site->clearRatingImageBlob();
    }
}
