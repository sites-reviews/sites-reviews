<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\ReviewRating;
use App\User;
use Tests\TestCase;

class SiteReviewUpDownPolicyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanRateUpIfOtherUser()
    {
        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertTrue($user->can('rateUp', $review));
    }

    public function testCantRateUpIfUserIsReviewCreator()
    {
        $review = factory(Review::class)
            ->create();

        $user = $review->create_user;

        $this->assertFalse($user->can('rateUp', $review));
    }

    public function testCanRateDownIfOtherUser()
    {
        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertTrue($user->can('rateUp', $review));
    }

    public function testCanRateDownIfUserIsReviewCreator()
    {
        $review = factory(Review::class)
            ->create();

        $user = $review->create_user;

        $this->assertFalse($user->can('rateUp', $review));
    }
}
