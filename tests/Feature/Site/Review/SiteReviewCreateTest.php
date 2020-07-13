<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\Site;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class SiteReviewCreateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStoreIsOk()
    {
        $user = factory(User::class)
            ->create();

        $site = factory(Site::class)
            ->create();

        $review = factory(Review::class)
            ->make();

        Carbon::setTestNow(now()->addMinute());

        $this->actingAs($user)
            ->post(route('reviews.store', ['site' => $site]), [
                'advantages' => $review->advantages,
                'disadvantages' => $review->disadvantages,
                'comment' => $review->comment,
                'rate' => $review->rate
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('sites.show', $site))
            ->assertSessionHas(['success' => __('review.successfully_published')]);

        $createdReview = $user->reviews()->first();

        $user->refresh();
        $site->refresh();

        $this->assertNotNull($createdReview);
        $this->assertEquals($review->advantages, $createdReview->advantages);
        $this->assertEquals($review->disadvantages, $createdReview->disadvantages);
        $this->assertEquals($review->comment, $createdReview->comment);
        $this->assertEquals($review->rate, $createdReview->rate);
        $this->assertEquals($site->updated_at, $site->latest_rating_changes_at);

        $this->assertEquals(1, $user->number_of_reviews);
        $this->assertEquals(1, $site->number_of_reviews);

        $this->assertTrue($createdReview->is($site->reviews()->first()));

        $this->assertEquals($review->rate, $site->rating);
    }
}
