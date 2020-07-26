<?php

namespace Tests\Feature;

use App\Site;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test()
    {
        $term = Str::random(8);

        $site = factory(Site::class)
            ->create(['title' => $term]);

        $response = $this->get(route('sites.search', ['term' => mb_substr($term, 0, -2)]))
            ->assertOk()
            ->assertSeeText($site->title)
            ->assertViewHas('isDomain', false)
            ->assertViewHas('addSite', false)
            ->assertViewHas('domain', null);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUrl()
    {
        $site = factory(Site::class)
            ->make();

        $response = $this->get(route('sites.search', ['term' => (string)$site->getUrl()]))
            ->assertOk()
            ->assertViewHas('isDomain', true)
            ->assertViewHas('addSite', true)
            ->assertViewHas('domain', $site->domain);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDomain()
    {
        $site = factory(Site::class)
            ->make();

        $domain = mb_strtoupper($site->domain);

        $response = $this->get(route('sites.search', ['term' => $domain]))
            ->assertOk()
            ->assertViewHas('isDomain', true)
            ->assertViewHas('addSite', true)
            ->assertViewHas('domain', $site->domain);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDomainWithWWW()
    {
        $site = factory(Site::class)
            ->make();

        $domain = 'www.'.$site->domain;

        $response = $this->get(route('sites.search', ['term' => $domain]))
            ->assertOk()
            ->assertViewHas('isDomain', true)
            ->assertViewHas('addSite', true)
            ->assertViewHas('domain', $site->domain);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEmpty()
    {
        $str = '';

        $response = $this->get(route('sites.search', ['term' => $str]))
            ->assertOk()
            ->assertViewHas('isDomain', false)
            ->assertViewHas('domain', '');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearchUrl()
    {
        $site = factory(Site::class)
            ->create();

        $response = $this->get(route('sites.search', ['term' => (string)$site->getUrl()]))
            ->assertRedirect(route('sites.show', ['site' => $site]));
            /*
            ->assertOk()
            ->assertViewHas('term', (string)$site->getUrl())
            ->assertViewHas('isDomain', true)
            ->assertViewHas('domain', $site->domain)
            ->assertViewHas('addSite', false);


        $sites = $response->original->gatherData()['sites'];

        $this->assertEquals(1, $sites->count());
        $this->assertEquals($site->domain, $sites->first()->domain);
             */
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIfSearchDomainWithWWW()
    {
        $site = factory(Site::class)
            ->create();

        $domainWithWWW = $site->getUrl()->withHost('www.'.$site->getUrl()->getHost());

        $response = $this->get(route('sites.search', ['term' => (string)$domainWithWWW]))
            ->assertRedirect(route('sites.show', ['site' => $site]));
            /*
            ->assertOk()
            ->assertViewHas('term', (string)$domainWithWWW)
            ->assertViewHas('isDomain', true)
            ->assertViewHas('domain', $site->domain)
            ->assertViewHas('addSite', false);
        */
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRedirectToSiteIfOnlyOneFound()
    {
        $site = factory(Site::class)
            ->create();

        $response = $this->get(route('sites.search', ['term' => (string)$site->domain]))
            ->assertRedirect(route('sites.show', ['site' => $site]));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearchIfFoundDomainAndOtherSites()
    {
        $site = factory(Site::class)
            ->create(['domain' => mb_strtolower(Str::random(4)).'.com']);

        $host = (string)$site->getUrl()->getHost();

        $site2 = factory(Site::class)
            ->create(['title' => $host]);

        $response = $this->get(route('sites.search', ['term' => $host]))
            ->assertOk()
            ->assertViewHas('isDomain', true)
            ->assertViewHas('addSite', false)
            ->assertViewHas('domain', $host);
    }
}
