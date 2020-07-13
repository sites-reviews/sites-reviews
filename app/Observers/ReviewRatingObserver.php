<?php

namespace App\Observers;

use App\Image;
use App\Notifications\ReviewWasLikedNotification;
use App\Review;
use App\ReviewRating;
use App\Site;
use App\User;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Imagick;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use Litlife\Url\Url;

class ReviewRatingObserver
{
    public function creating(ReviewRating $rating)
    {
        $rating->autoAssociateAuthUser();
    }

	/**
	 * Listen to the User created event.
	 *
	 * @param Review $review
	 * @return void
	 */
	public function created(ReviewRating $rating)
	{
        if (!empty($rating->rateable))
            $this->ratingsReviewUpdate($rating->rateable);

        if (!empty($rating->rateable->create_user))
            $this->updateReviewCreatorRating($rating->rateable->create_user);

        $rating->rateable
            ->create_user
            ->notify(new ReviewWasLikedNotification($rating));
	}

    public function updated(ReviewRating $rating)
    {
        if ($rating->wasChanged('rating'))
        {
            if (!empty($rating->rateable))
                $this->ratingsReviewUpdate($rating->rateable);

            if (!empty($rating->rateable->create_user))
                $this->updateReviewCreatorRating($rating->rateable->create_user);
        }
    }

	public function deleted(ReviewRating $rating)
	{
        if (!empty($rating->rateable))
            $this->ratingsReviewUpdate($rating->rateable);

        if (!empty($rating->rateable->create_user))
            $this->updateReviewCreatorRating($rating->rateable->create_user);
	}

	public function restored(ReviewRating $rating)
	{
        if (!empty($rating->rateable))
            $this->ratingsReviewUpdate($rating->rateable);

        if (!empty($rating->rateable->create_user))
            $this->updateReviewCreatorRating($rating->rateable->create_user);
	}

	public function ratingsReviewUpdate(Review $review)
    {
        $review->ratingsUpdate();
        $review->save();
    }

    public function updateReviewCreatorRating(User $user)
    {
        $user->updateRating();
        $user->save();
    }
}
