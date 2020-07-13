<?php

namespace App\Observers;

use App\Image;
use App\Notifications\NewResponseToReviewNotification;
use App\Notifications\NewResponseToYourCommentNotification;
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

class CommentObserver
{
    public function creating(Comment $comment)
    {
        $comment->autoAssociateAuthUser();
        $comment->level = $comment->getLevel();
    }

    public function created(Comment $comment)
    {
        $this->updateCommentCount($comment->review);

        $parent = $comment->getParent();

        if (!empty($parent))
        {
            $this->updateChildrenCount($parent);

            $parent->create_user
                ->notify(new NewResponseToYourCommentNotification($comment));
        }
        else
        {
            $comment->review
                ->create_user
                ->notify(new NewResponseToReviewNotification($comment));
        }
    }

    public function restored(Comment $comment)
    {
        $this->updateCommentCount($comment->review);

        $parent = $comment->getParent();

        if (!empty($parent))
            $this->updateChildrenCount($parent);
    }

    public function deleted(Comment $comment)
    {
        $this->updateCommentCount($comment->review);

        $parent = $comment->getParent();

        if (!empty($parent))
            $this->updateChildrenCount($parent);
    }

    public function updateCommentCount(Review $review)
    {
        $review->updateChildrenCount();
        $review->save();
    }

    public function updateChildrenCount(Comment $comment)
    {
        $comment->updateChildrenCount();
        $comment->save();
    }
}
