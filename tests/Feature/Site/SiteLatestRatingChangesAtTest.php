<?php

namespace Tests\Feature\Site;

use App\Site;
use Tests\TestCase;

class SiteLatestRatingChangesAtTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDefault()
    {
        $site = factory(Site::class)
            ->create();

        $this->assertNotNull($site->latest_rating_changes_at);
    }
}
