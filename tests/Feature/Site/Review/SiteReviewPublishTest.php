<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\Site;
use App\User;
use Tests\TestCase;

class SiteReviewPublishTest extends TestCase
{
    public function testIfNotAjax()
    {
        $review = factory(Review::class)
            ->states('private')
            ->create();

        $user = $review->create_user;

        $this->actingAs($user)
            ->get(route('reviews.publish', $review))
            ->assertRedirect()
            ->assertSessionHas('success', __('A review is successfully published'));

        $review->refresh();

        $this->assertTrue($review->isAccepted());
    }

    public function testIfOtherReviewExists()
    {
        $user = factory(User::class)->create();

        $site = factory(Site::class)->create();

        $review = factory(Review::class)
            ->states('accepted')
            ->create([
                'create_user_id' => $user->id,
                'site_id' => $site->id
            ]);

        $review2 = factory(Review::class)
            ->states('private')
            ->create([
                'create_user_id' => $user->id,
                'site_id' => $site->id
            ]);

        $this->actingAs($user)
            ->ajax()
            ->get(route('reviews.publish', $review2))
            ->assertOk();

        $review->refresh();
        $review2->refresh();

        $this->assertTrue($review->isPrivate());
        $this->assertTrue($review2->isAccepted());
    }
}
