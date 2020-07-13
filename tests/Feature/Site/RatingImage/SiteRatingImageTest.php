<?php

namespace Tests\Feature\Site\RatingImage;

use App\Site;
use Tests\TestCase;

class SiteRatingImageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIsOk()
    {
        $site = factory(Site::class)->create();

        $response = $this->get(route('sites.rating.image', $site))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            //->assertHeader('Last-Modified', $site->latest_rating_changes_at->format('D, d M Y H:i:s').' GMT')
            ->assertHeader('Expires', now()->addHour()->format('D, d M Y H:i:s').' GMT')
            ->assertHeader('Pragma', 'cache')
            ->assertHeader('Content-length', strlen($site->getRatingImageBlob()))
            ->assertHeader('Cache-control', 'max-age=3600, public, s-maxage=3600');
    }
}
