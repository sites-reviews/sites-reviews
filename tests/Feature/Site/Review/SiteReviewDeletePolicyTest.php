<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteReviewDeletePolicyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanDeleteIfCreator()
    {
        $review = factory(Review::class)
            ->create();

        $create_user = $review->create_user;

        $this->assertTrue($create_user->can('delete', $review));
    }

    public function testCantDeleteIfOtherCreator()
    {
        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->assertFalse($user->can('delete', $review));
    }

    public function testCantDeleteIfDeleted()
    {
        $review = factory(Review::class)
            ->create();

        $review->delete();

        $create_user = $review->create_user;

        $this->assertFalse($create_user->can('delete', $review));
    }
}
