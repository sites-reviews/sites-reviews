<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\User;
use Tests\TestCase;

class SiteReviewsPublishPolicyTest extends TestCase
{
    public function testCan()
    {
        $review = factory(Review::class)
            ->states('private')
            ->create();

        $user = $review->create_user;

        $this->assertTrue($user->can('publish', $review));
    }

    public function testCantIfAccepted()
    {
        $review = factory(Review::class)
            ->states('accepted')
            ->create();

        $user = $review->create_user;

        $this->assertFalse($user->can('publish', $review));
    }

    public function testCantIfOtherUser()
    {
        $review = factory(Review::class)
            ->states('private')
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertFalse($user->can('publish', $review));
    }
}
