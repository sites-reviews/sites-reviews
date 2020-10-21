<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\User;
use Tests\TestCase;

class SiteReviewShowPolicyTest extends TestCase
{
    public function testCanIfAccepted()
    {
        $review = factory(Review::class)
            ->states('accepted')
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertTrue($user->can('show', $review));
    }

    public function testCantIfPrivateAndOtherUser()
    {
        $review = factory(Review::class)
            ->states('private')
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertFalse($user->can('show', $review));
    }

    public function testCanIfPrivateAndUserIsCreator()
    {
        $review = factory(Review::class)
            ->states('private')
            ->create();

        $user = $review->create_user;

        $this->assertTrue($user->can('show', $review));
    }
}
