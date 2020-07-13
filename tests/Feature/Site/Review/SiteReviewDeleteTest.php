<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use Carbon\Carbon;
use Tests\TestCase;

class SiteReviewDeleteTest extends TestCase
{
    public function testDelete()
    {
        $review = factory(Review::class)
            ->create();

        $site = $review->site;
        $user = $review->create_user;

        Carbon::setTestNow(now()->addMinute());

        $this->actingAs($user)
            ->delete(route('reviews.destroy', ['review' => $review]))
            ->assertRedirect(route('sites.show', $site))
            ->assertSessionHas(['success' => __('The review was successfully deleted')]);

        $review->refresh();
        $user->refresh();
        $site->refresh();

        $this->assertEquals($site->updated_at, $site->latest_rating_changes_at);

        $this->assertTrue($review->trashed());
        $this->assertEquals(0, $site->rating);
    }

    public function testRestore()
    {
        $review = factory(Review::class)
            ->create();

        $user = $review->create_user;

        $site = $review->site;

        $review->delete();

        Carbon::setTestNow(now()->addMinute());

        $this->actingAs($user)
            ->delete(route('reviews.destroy', ['review' => $review]))
            ->assertRedirect(route('sites.show', $site))
            ->assertSessionHas(['success' => __('The review was successfully restored')]);

        $review->refresh();
        $user->refresh();
        $site->refresh();

        $this->assertEquals($site->updated_at, $site->latest_rating_changes_at);

        $this->assertFalse($review->trashed());
        $this->assertEquals($review->rate, $site->rating);
    }
}
