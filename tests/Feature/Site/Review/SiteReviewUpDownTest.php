<?php

namespace Tests\Feature\Site\Review;

use App\Notifications\ReviewWasLikedNotification;
use App\Review;
use App\ReviewRating;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SiteReviewUpDownTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUp()
    {
        Notification::fake();

        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('reviews.rate.up', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonFragment(['rateable' => ['rating' => 1]]);

        $review->refresh();

        $rating = $review->ratings()->first();

        $this->assertNotNull($rating);
        $this->assertEquals(1, $rating->rating);
        $this->assertTrue($user->is($rating->create_user));

        $this->assertEquals(1, $review->rate_up);
        $this->assertEquals(0, $review->rate_down);
        $this->assertEquals(1, $review->rating);

        $notifiable = $review->create_user;

        Notification::assertSentTo($notifiable,
            ReviewWasLikedNotification::class,
            function ($notification, $channels) use ($rating, $notifiable) {
                $this->assertNotContains('mail', $channels);
                $this->assertContains('database', $channels);

                $data = $notification->toArray($notifiable);

                $this->assertEquals(__('Someone liked your review'), $data['title']);
                $this->assertEquals(__(':userName liked your review', ['userName' => $rating->create_user->name]), $data['description']);
                $this->assertEquals(route('reviews.go_to', $rating->rateable), $data['url']);

                return $notification->reviewVote->id == $rating->id;
            }
        );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDown()
    {
        Notification::fake();

        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('reviews.rate.down', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonFragment(['rateable' => ['rating' => -1]]);

        $review->refresh();

        $rating = $review->ratings()->first();

        $this->assertNotNull($rating);
        $this->assertEquals(-1, $rating->rating);
        $this->assertTrue($user->is($rating->create_user));

        $this->assertEquals(0, $review->rate_up);
        $this->assertEquals(1, $review->rate_down);
        $this->assertEquals(-1, $review->rating);

        $notifiable = $review->create_user;

        Notification::assertSentTo($notifiable,
            ReviewWasLikedNotification::class,
            function ($notification, $channels) use ($rating, $notifiable) {
                $this->assertNotContains('mail', $channels);
                $this->assertNotContains('database', $channels);

                return $notification->reviewVote->id == $rating->id;
            }
        );
    }

    public function testVoteUpIfVoteDownExists()
    {
        $rating = factory(ReviewRating::class)
            ->states('down')
            ->create();

        $review = $rating->rateable;
        $user = $rating->create_user;

        $response = $this->actingAs($user)
            ->get(route('reviews.rate.up', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $rating->refresh();
        $review->refresh();

        $this->assertEquals(1, $rating->rating);
        $this->assertEquals(1, $review->rating);
        $this->assertEquals(1, $review->create_user->rating);
    }

    public function testVoteDownIfVoteUpExists()
    {
        $rating = factory(ReviewRating::class)
            ->states('up')
            ->create();

        $review = $rating->rateable;
        $user = $rating->create_user;

        $response = $this->actingAs($user)
            ->get(route('reviews.rate.down', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $rating->refresh();
        $review->refresh();

        $this->assertEquals(-1, $rating->rating);
        $this->assertEquals(-1, $review->rating);
        $this->assertEquals(-1, $review->create_user->rating);
    }

    public function testVoteUpIfVoteUpExists()
    {
        $rating = factory(ReviewRating::class)
            ->states('up')
            ->create();

        $review = $rating->rateable;
        $user = $rating->create_user;

        $response = $this->actingAs($user)
            ->get(route('reviews.rate.up', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $rating->refresh();
        $user->refresh();
        $review->refresh();

        $this->assertEquals(0, $rating->rating);
        $this->assertEquals(0, $review->number_of_votes);
        $this->assertEquals(0, $user->number_of_rated_reviews);
        $this->assertEquals(0, $review->rating);
        $this->assertEquals(0, $review->create_user->rating);
    }

    public function testVoteDownIfVoteDownExists()
    {
        $rating = factory(ReviewRating::class)
            ->states('up')
            ->create();

        $review = $rating->rateable;
        $user = $rating->create_user;

        $response = $this->actingAs($user)
            ->get(route('reviews.rate.up', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $rating->refresh();
        $user->refresh();
        $review->refresh();

        $this->assertEquals(0, $rating->rating);
        $this->assertEquals(0, $review->number_of_votes);
        $this->assertEquals(0, $user->number_of_rated_reviews);
        $this->assertEquals(0, $review->rating);
        $this->assertEquals(0, $review->create_user->rating);
    }

    public function testRestoreUpVote()
    {
        $rating = factory(ReviewRating::class)
            ->states('up')
            ->create();

        $review = $rating->rateable;
        $user = $rating->create_user;

        $rating->delete();

        $response = $this->actingAs($user)
            ->get(route('reviews.rate.up', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $rating->refresh();
        $review->refresh();
        $this->assertFalse($rating->trashed());
        $this->assertEquals(1, $rating->rating);
        $this->assertEquals(1, $review->rating);
        $this->assertEquals(1, $review->create_user->rating);
    }

    public function testRestoreDownVote()
    {
        $rating = factory(ReviewRating::class)
            ->states('down')
            ->create();

        $review = $rating->rateable;
        $user = $rating->create_user;

        $rating->delete();

        $response = $this->actingAs($user)
            ->get(route('reviews.rate.down', $review), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $rating->refresh();
        $review->refresh();
        $this->assertFalse($rating->trashed());
        $this->assertEquals(-1, $rating->rating);
        $this->assertEquals(-1, $review->rating);
        $this->assertEquals(-1, $review->create_user->rating);
    }
}
