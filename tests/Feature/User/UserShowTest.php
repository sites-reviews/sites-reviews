<?php

namespace Tests\Feature\User;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserShowTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShow()
    {
        $user = factory(User::class)
            ->create();

        $this->get(route('users.show', $user))
            ->assertOk()
            ->assertViewHas('user', $user)
            ->assertViewHas('reviews', null);
    }

    public function testShowWithReview()
    {
        $review = factory(Review::class)
            ->create();

        $user = $review->create_user;

        $this->get(route('users.show', $user))
            ->assertOk()
            ->assertViewHas('user', $user)
            ->assertSeeText($review->advantages)
            ->assertSeeText($review->disadvantages)
            ->assertSeeText($review->comment);
    }
}
