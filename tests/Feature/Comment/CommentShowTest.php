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

class CommentShowTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRouteIsOk()
    {
        $comment = factory(Comment::class)
            ->create();

        $this->get(route('comments.show', ['comment' => $comment]))
            ->assertOk()
            ->assertViewHas('comment', $comment);
    }
}
