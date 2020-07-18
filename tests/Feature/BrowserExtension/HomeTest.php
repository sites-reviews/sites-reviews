<?php

namespace Tests\Feature\BrowserExtension;

use App\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Litlife\Url\Url;
use Tests\TestCase;

class BrowserExtensionTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRedirectToCreateOrShowIfUrlHasHost()
    {
        $url = Url::fromString('https://example.com/test');

        $this->get(route('browser.extension.redirect', ['url' => (string)$url]))
            ->assertRedirect(route('sites.create.or_show', ['domain' => $url->getHost()]));
    }

    public function testRedirectToSiteSearchIfUrlDoesntHaveHost()
    {
        $url = Url::fromString('/test');

        $term = (string)$url;

        $this->get(route('browser.extension.redirect', ['url' => (string)$url]))
            ->assertRedirect(route('sites.search', ['term' => $term]));
    }
}
