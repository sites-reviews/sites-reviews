<?php

namespace Tests\Feature\User\Review;

use App\Review;
use Tests\TestCase;

class UserReviewDraftTest extends TestCase
{
    public function testSeeDrafts()
    {
        $review = factory(Review::class)
            ->states('private')
            ->create();

        $user= $review->create_user;

        $this->actingAs($user)
            ->get(route('users.reviews.draft', ['user' => $user]))
            ->assertOk();
    }
}
