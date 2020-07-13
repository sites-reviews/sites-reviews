<?php

namespace Tests\Feature\Site\Review;

use App\Notifications\ReviewWasLikedNotification;
use App\Review;
use App\ReviewRating;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SiteReviewShowTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test()
    {
        $review = factory(Review::class)
            ->create();

        $this->get(route('reviews.show', $review))
            ->assertOk()
            ->assertViewHas('review', $review);
    }
}
