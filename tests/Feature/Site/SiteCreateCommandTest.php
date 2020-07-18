<?php

namespace Tests\Feature\Site;

use App\Review;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7;

class SiteCreateCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOk()
    {
        $siteNew = factory(Site::class)
            ->make();

        $this->artisan('site:create', ['url' => $siteNew->getUrl()])
            ->expectsOutput(__('The site was added successfully'))
            ->assertExitCode(1);

        $site = Site::whereDomain($siteNew->domain)->first();

        $this->assertNotNull($site);
        $this->assertEquals($site->domain, $siteNew->domain);
        $this->assertTrue($site->update_the_preview);
        $this->assertTrue($site->update_the_page);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testWrongUrl()
    {
        $this->artisan('site:create', ['url' => '123123'])
            ->expectsOutput(__('Wrong URL'))
            ->assertExitCode(0);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteAlreadyInDB()
    {
        $site = factory(Site::class)
            ->create();

        $this->artisan('site:create', ['url' => $site->getUrl()])
            ->expectsOutput(__('The site is already in the database'))
            ->assertExitCode(0);
    }
}
