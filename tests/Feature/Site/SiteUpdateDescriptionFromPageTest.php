<?php

namespace Tests\Feature\Site;

use App\Review;
use App\Site;
use App\SitePage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7;

class SiteUpdateDescriptionFromPageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDescriptionFromTitle()
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

        $site = $page->site;
        $site->description = null;
        $site->save();

        $site->updateDescriptionFromPage();

        $this->assertEquals('', $site->desciption);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDescriptionFromDescription()
    {
        $content = <<<EOF
<html>
<head>
<title>Title</title>
<meta name="description" content="Description" />
</head>
<body><p>text</p></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $site = $page->site;
        $site->description = null;
        $site->save();

        $site->updateDescriptionFromPage();

        $this->assertEquals('Description', $site->description);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFillEmptyDescriptionIfOpenGraphDescriptionExists()
    {
        $content = <<<EOF
<html>
<head>
<title>Title</title>
<meta property="og:description" content="Open graph description" />
<meta name="twitter:description" content="Description for twitter">
</head>
<body><p>text</p></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $site = $page->site;
        $site->description = null;
        $site->save();

        $site->updateDescriptionFromPage();

        $this->assertEquals('Open graph description', $site->description);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFillEmptyDescriptionIfTwitterDescriptionExists()
    {
        $content = <<<EOF
<html>
<head>
<title>Title</title>
<meta name="twitter:description" content="Description for twitter">
</head>
<body><p>text</p></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $site = $page->site;
        $site->description = null;
        $site->save();

        $site->updateDescriptionFromPage();

        $this->assertEquals('Description for twitter', $site->description);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFillEmptyDescriptionIfPageDoesntHaveTitle()
    {
        $content = <<<EOF
<html>
<head>
<meta name="twitter:description" content="Description for twitter">
</head>
<body><p>text</p></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $site = $page->site;
        $site->description = null;
        $site->save();

        $site->updateDescriptionFromPage();

        $this->assertEquals('Description for twitter', $site->description);
    }
}
