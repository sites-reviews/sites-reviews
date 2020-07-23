<?php

namespace Tests\Feature\Site\Page\SearchForNewDomainsCommand;

use App\PossibleDomain;
use App\SitePage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SitePageSearchForNewDomainsCommandGetHostTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test1()
    {
        $host = mb_strtolower(Str::random(10)).'.com';
        $url = 'http://'.$host.'/';

        $content = <<<EOF
<html>
<body>
<a href="$url">ссылка</a>
</body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $this->assertNull($page->search_for_new_domains_is_completed_at);

        $this->artisan('site_page:search_for_new_domains', ['latest_id' => $page->id])
            ->assertExitCode(1);

        $page->refresh();

        $this->assertNotNull($page->search_for_new_domains_is_completed_at);

        $possibleDomain = PossibleDomain::whereDomain($host)->first();

        $this->assertNotNull($possibleDomain);
        $this->assertEquals($host, $possibleDomain->domain);
    }
}
