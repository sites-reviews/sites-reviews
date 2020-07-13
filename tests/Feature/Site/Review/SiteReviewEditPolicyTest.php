<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteReviewEditPolicyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanEditIfCreator()
    {
        $review = factory(Review::class)
            ->create();

        $create_user = $review->create_user;

        $this->assertTrue($create_user->can('edit', $review));
    }

    public function testCantEditIfOtherCreator()
    {
        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertFalse($user->can('edit', $review));
    }
}
