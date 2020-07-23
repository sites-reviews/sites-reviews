<?php

namespace Tests\Unit\Site\Page;

use App\SitePage;
use PHPUnit\Framework\TestCase;

class SitePageGetMetaTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDescription()
    {
        $content = <<<EOF
<html>
<head>
<title>привет</title>
<meta name="description" content="Текст">
</head>
<body>
</body>
</html>
EOF;

        $page = new SitePage();
        $page->content = $content;

        $this->assertEquals('Текст', $page->getMetaData()["description"]);
    }
}
