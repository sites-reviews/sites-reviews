<?php

namespace Tests\Feature\Site;

use App\Site;
use Litlife\Url\Url;
use Tests\TestCase;

class SiteGetUrlWithUtmTest extends TestCase
{
    public function test()
    {
        $site = new Site();
        $site->domain = 'example.com';

        $host = Url::fromString(config('app.url'))
            ->getHost();

        $this->assertEquals('http://example.com/?utm_medium=company_profile&utm_source='.$host.'&utm_campaign=logo_click',
            (string)$site->getUrlWithUtm());
    }
}
