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

class CommentChildrenTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRouteIsOk()
    {
        $parent = factory(Comment::class)
            ->create();

        $comment = factory(Comment::class)
            ->create(['parent' => $parent]);

        $user = factory(User::class)
            ->create();

        $this->get(route('comments.children', ['comment' => $parent]))
            ->assertOk();
    }
}
