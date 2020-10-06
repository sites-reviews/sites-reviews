<?php

namespace Tests\Feature\Site\Page;

use App\Review;
use App\Site;
use App\SitePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class SitePageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDOM()
    {
        $content = '<html><head><title>Title</title></head><body><p>text</p></body></html>';

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $this->assertInstanceOf(\DOMDocument::class, $page->dom());
    }

    public function testGetXpath()
    {
        $content = '<html><head><title>Title</title></head><body><p>text</p></body></html>';

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $this->assertInstanceOf(\DOMXPath::class, $page->xpath());
    }

    public function testGetBody()
    {
        $content = '<html><head><title>Title</title></head><body><p>text</p></body></html>';

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $body = $page->body();

        $this->assertInstanceOf(\DOMElement::class, $body);
        $this->assertEquals('<body><p>text</p></body>', $page->dom()->saveHtml($body));
    }

    public function testGetHead()
    {
        $content = '<html><head><title>Title</title></head><body><p>text</p></body></html>';

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $head = $page->head();

        $this->assertInstanceOf(\DOMElement::class, $head);
        $this->assertEquals('<head><title>Title</title></head>', $page->dom()->saveHtml($head));
    }

    public function testGetMetaData()
    {
        $content = <<<EOF
<html>
<head>
<title>Title</title>
<meta property="og:type" content="website" />
<meta name="twitter:description" content="Description" />
<meta property="og:locale" />
<meta name="keywords">
</head>
<body><p>text</p></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $array = $page->getMetaData();

        $this->assertArrayHasKey('og:type', $array);
        $this->assertEquals($array['og:type'], 'website');

        $this->assertArrayHasKey('twitter:description', $array);
        $this->assertEquals($array['twitter:description'], 'Description');

        $this->assertArrayHasKey('og:locale', $array);
        $this->assertEquals($array['og:locale'], '');

        $this->assertArrayHasKey('keywords', $array);
        $this->assertEquals($array['keywords'], '');
    }

    public function testHeaderTag()
    {
        $content = <<<EOF
<html>
<head>
<title> Title</title>
</head>
<body><header><div>test</div></header></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $this->assertEquals('Title', $page->getTitleValue());
    }
}
