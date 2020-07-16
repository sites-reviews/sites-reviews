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

        $this->assertNull(Cache::get($site->id . ':ri:1x'));

        $this->assertNotEmpty($site->getRatingImageBlob());

        $this->assertNotNull(Cache::get($site->id . ':ri:1x'));
    }

    public function testClear()
    {
        $site = factory(Site::class)->create();

        $this->assertNotEmpty($site->getRatingImageBlob());

        $this->assertNotNull(Cache::get($site->id . ':ri:1x'));

        $this->assertTrue($site->clearRatingImageBlob());

        $this->assertNull(Cache::get($site->id . ':ri:1x'));
    }
}
