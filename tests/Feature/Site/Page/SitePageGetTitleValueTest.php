<?php

namespace Tests\Feature\Site\Page;

use App\SitePage;
use Tests\TestCase;

class SitePageGetTitleValueTest extends TestCase
{
    public function testGetTitleValue()
    {
        $content = <<<EOF
<html>
<head>
<title> Title</title>
</head>
<body><p>text</p></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $this->assertEquals('Title', $page->getTitleValue());
    }

    public function testGetTitleValueEntities()
    {
        $content = <<<EOF
<html>
<head>
<title>Тест &quot;тест&quot;</title>
</head>
<body><p>text</p></body>
</html>
EOF;

        $page = factory(SitePage::class)
            ->create(['content' => $content]);

        $this->assertEquals('Тест "тест"', $page->getTitleValue());
    }
}
