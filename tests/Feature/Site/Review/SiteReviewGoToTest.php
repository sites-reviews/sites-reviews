<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use Tests\TestCase;

class SiteReviewGoToTest extends TestCase
{
    public function testGetUrl()
    {
        $review = factory(Review::class)
            ->create();

        $this->assertEquals(
            route('reviews.go_to', ['review' => $review]),
            $review->getGoToUrl());
    }

    public function testRedirect()
    {
        $review = factory(Review::class)
            ->create();

        $this->get(route('reviews.go_to', ['review' => $review]))
            ->assertRedirect(route('sites.show', ['site' => $review->site]).'#review'.$review->id);
    }
}
