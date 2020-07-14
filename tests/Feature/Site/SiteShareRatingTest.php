<?php

namespace Tests\Feature\Site;

use App\Review;
use App\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiteShareRatingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testButtonHtmlCode()
    {
        $site = factory(Site::class)
            ->create();

        $content = "<a href=\"" . route('sites.show', $site) . "\">" . "\n" .
            "<img src=\"".route('sites.rating.image', $site)."\" width=\"88\" height=\"31\" border=\"0\" alt=\"".htmlspecialchars($site->buttonImageAltText())."\" />" . "\n" .
            "</a>";

        $this->assertEquals($content, $site->buttonHtmlCode());
    }

    public function testButtonBBCode()
    {
        $site = factory(Site::class)
            ->create();

        $content = '[url='.route('sites.show', $site).']'."\n".
            '[img]'.route('sites.rating.image', $site).'[/img]'."\n".
            '[/url]';

        $this->assertEquals($content, $site->buttonBBCode());
    }

    public function testButtonImageAltText()
    {
        $site = factory(Site::class)
            ->create();

        $this->assertEquals(__('Rating and reviews of the site').' '.$site->domain,
            $site->buttonImageAltText());
    }
}
