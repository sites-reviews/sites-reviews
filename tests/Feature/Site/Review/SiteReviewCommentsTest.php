<?php

namespace Tests\Feature\Site\Review;

use App\Notifications\NewResponseToReviewNotification;
use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SiteReviewCommentsTest extends TestCase
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

        $review = $comment->review;

        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->get(route('reviews.comments', ['review' => $review]))
            ->assertOk();
    }
}
