<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use Carbon\Carbon;
use Tests\TestCase;

class SiteReviewUpdateTest extends TestCase
{
    public function testUpdateRouteIsOk()
    {
        $review = factory(Review::class)
            ->create();

        $user = $review->create_user;

        $this->actingAs($user)
            ->get(route('reviews.edit', ['review' => $review]))
            ->assertOk()
            ->assertViewIs('site.review.edit')
            ->assertSeeText(__('review.rate'))
            ->assertSeeText(__('review.advantages'))
            ->assertSeeText(__('review.disadvantages'))
            ->assertSeeText(__('review.comment'))
            ->assertSeeText(__('Save'));
    }

    public function testPatchRouteIsOk()
    {
        $review = factory(Review::class)
            ->create();

        $reviewNew = factory(Review::class)
            ->make();

        $site = $review->site;
        $user = $review->create_user;

        Carbon::setTestNow(now()->addMinute());

        $this->actingAs($user)
            ->patch(route('reviews.update', ['review' => $review]), [
                'advantages' => $reviewNew->advantages,
                'disadvantages' => $reviewNew->disadvantages,
                'comment' => $reviewNew->comment,
                'rate' => $reviewNew->rate
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas(['success' => __('The review was updated successfully')]);

        $review->refresh();
        $user->refresh();
        $site->refresh();

        $this->assertEquals($site->updated_at, $site->latest_rating_changes_at);

        $this->assertEquals($reviewNew->advantages, $review->advantages);
        $this->assertEquals($reviewNew->disadvantages, $review->disadvantages);
        $this->assertEquals($reviewNew->comment, $review->comment);
        $this->assertEquals($reviewNew->rate, $review->rate);

        $this->assertEquals($review->rate, $site->rating);
    }
}
