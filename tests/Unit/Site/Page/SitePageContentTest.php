<?php

namespace Tests\Unit\Site\Page;

use App\Site;
use App\SitePage;
use PHPUnit\Framework\TestCase;

class SitePageContentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDefault()
    {
        $page = new SitePage();

        $this->assertEquals('', $page->content);
    }

    public function testTrim()
    {
        $page = new SitePage();
        $page->content = '    content   ';

        $this->assertEquals('content', $page->content);
    }

    public function testRemoveLoaded()
    {
        $page = new SitePage();
        $page->content = '495,28 kB (204,8 kB loaded)<!DOCTYPE html>';

        $this->assertEquals('<!DOCTYPE html>', $page->content);
    }

    public function testEncodingConvert()
    {
        $page = new SitePage();
        $page->content = iconv('utf-8', 'windows-1251', 'привет');

        $this->assertEquals('      ', $page->content);
    }

    public function testSetGetString()
    {
        $page = new SitePage();
        $page->content = 'привет';

        $this->assertEquals('привет', $page->content);
    }

    public function testForceUtf8EncodeIfAutoConvertEncodingIsFail()
    {
        $page = new SitePage();
        $page->content = iconv('UTF-8', 'windows-1255', 'היי, מה שלומך?').' hello';

        $this->assertEquals('   ,         ? hello', $page->content);
    }
}
