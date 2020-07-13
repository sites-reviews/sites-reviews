<?php

namespace App\Policies;

use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy extends Policy
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

    public function reply(User $authUser, Comment $comment)
    {
        if ($comment->isUserCreator($authUser))
            return $this->deny(__("You can't respond to your comment"));
        else
            return $this->allow();
    }

    public function edit(User $authUser, Comment $comment)
    {
        if ($comment->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__("You can't edit this comment"));
    }

    public function delete(User $authUser, Comment $comment)
    {
        if ($comment->trashed())
            return $this->deny(__('The comment has already been deleted'));

        if ($comment->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__("You can't delete this comment"));
    }

    public function restore(User $authUser, Comment $comment)
    {
        if (!$comment->trashed())
            return $this->deny(__("The comment was not deleted"));

        if ($comment->isUserCreator($authUser))
            return $this->allow();
        else
            return $this->deny(__("You can't restore this comment"));
    }

    public function rateUp(User $authUser, Comment $comment)
    {
        if ($comment->isUserCreator($authUser))
            return $this->deny(__("You can't rate your comments"));
        else
            return $this->allow();
    }

    public function rateDown(User $authUser, Comment $comment)
    {
        if ($comment->isUserCreator($authUser))
            return $this->deny(__("You can't rate your comments"));
        else
            return $this->allow();
    }
}
