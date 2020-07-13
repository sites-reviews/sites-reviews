<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteReviewReplyPolicyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCantIfUserCreatorOfReview()
    {
        $review = factory(Review::class)
            ->create();

        $create_user = $review->create_user;

        $this->assertFalse($create_user->can('reply', $review));
    }

    public function testCantIfNotCreatorOfReview()
    {
        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertTrue($user->can('reply', $review));
    }
}
