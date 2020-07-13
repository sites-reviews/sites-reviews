<?php

namespace Tests\Feature\Site\RatingImage;

use App\Site;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteRatingCacheTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGet()
    {
        $site = factory(Site::class)->create();

        $this->assertNull(Cache::get($site->id . ':ri'));

        $this->assertNotEmpty($site->getRatingImageBlob());

        $this->assertNotNull(Cache::get($site->id . ':ri'));
    }

    public function testClear()
    {
        $site = factory(Site::class)->create();

        $this->assertNotEmpty($site->getRatingImageBlob());

        $this->assertNotNull(Cache::get($site->id . ':ri'));

        $this->assertTrue($site->clearRatingImageBlob());

        $this->assertNull(Cache::get($site->id . ':ri'));
    }
}
