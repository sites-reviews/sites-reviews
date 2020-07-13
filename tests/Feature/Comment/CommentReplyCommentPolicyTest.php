<?php

namespace Tests\Feature\Comment;

use App\Notifications\NewResponseToReviewNotification;
use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentReplyCommentPolicyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanIfNotCreator()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertTrue($user->can('reply', $comment));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCantIfCreator()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = $comment->create_user;

        $this->assertFalse($user->can('reply', $comment));
    }
}
