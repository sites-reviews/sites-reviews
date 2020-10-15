<?php

namespace Tests\Feature\Site\Review;

use App\Review;
use App\Site;
use App\SiteOwner;
use Tests\TestCase;

class SiteReviewIsCreatorIsSiteOwnerTest extends TestCase
{
    public function testTrue()
    {
        $site = factory(Site::class)
            ->states('with_owner')
            ->create();

        $user = $site->userOwner;

        $review = factory(Review::class)
            ->create([
                'site_id' => $site->id,
                'create_user_id' => $user->id
            ]);

        $this->assertTrue($review->isCreatorIsSiteOwner());
    }

    public function testFalseIfNotConfirmed()
    {
        $site = factory(Site::class)
            ->states('with_owner')
            ->create();

        $user = $site->userOwner;

        $review = factory(Review::class)
            ->create([
                'site_id' => $site->id
            ]);

        $this->assertFalse($review->isCreatorIsSiteOwner());
    }
}
