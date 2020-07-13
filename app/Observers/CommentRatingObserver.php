<?php

namespace App\Observers;

use App\CommentRating;
use App\Image;
use App\Notifications\CommentWasLikedNotification;
use App\Notifications\ReviewWasLikedNotification;
use App\Review;
use App\Comment;
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

class CommentRatingObserver
{
    public function creating(CommentRating $rating)
    {
        $rating->autoAssociateAuthUser();
    }

	/**
	 * Listen to the User created event.
	 *
	 * @param CommentRating $rating
	 * @return void
	 */
	public function created(CommentRating $rating)
	{
        if (!empty($rating->rateable))
            $this->ratingsReviewUpdate($rating->rateable);

        if (!empty($rating->rateable->create_user))
            $this->updateReviewCreatorRating($rating->rateable->create_user);

        $rating->rateable
            ->create_user
            ->notify(new CommentWasLikedNotification($rating));
	}

    public function updated(CommentRating $rating)
    {
        if ($rating->wasChanged('rating'))
        {
            if (!empty($rating->rateable))
                $this->ratingsReviewUpdate($rating->rateable);

            if (!empty($rating->rateable->create_user))
                $this->updateReviewCreatorRating($rating->rateable->create_user);
        }
    }

	public function deleted(CommentRating $rating)
	{
        if (!empty($rating->rateable))
            $this->ratingsReviewUpdate($rating->rateable);

        if (!empty($rating->rateable->create_user))
            $this->updateReviewCreatorRating($rating->rateable->create_user);
	}

	public function restored(CommentRating $rating)
	{
        if (!empty($rating->rateable))
            $this->ratingsReviewUpdate($rating->rateable);

        if (!empty($rating->rateable->create_user))
            $this->updateReviewCreatorRating($rating->rateable->create_user);
	}

	public function ratingsReviewUpdate(Comment $comment)
    {
        $comment->ratingsUpdate();
        $comment->save();
    }

    public function updateReviewCreatorRating(User $user)
    {
        $user->updateRating();
        $user->save();
    }
}
