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

class CommentGetRedirectToUrlTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test()
    {
        $comment = factory(Comment::class)
            ->create();

        $this->assertEquals(route('comments.go_to', $comment),
            $comment->getRedirectToUrl());
    }
}
