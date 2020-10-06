<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteTitleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUcfirst()
    {
        $site = new Site();
        $site->title = 'test.com';

        $this->assertEquals('Test.com', $site->title);
    }

    public function testRemoveWWW()
    {
        $site = new Site();
        $site->title = 'wWw.test.com';

        $this->assertEquals('Test.com', $site->title);
    }

    public function testCyrDomain()
    {
        $site = new Site();
        $site->title = 'xn--2020-94damyi5albn6b6i.xn--p1ai';

        $this->assertEquals('Конституция2020.рф', $site->title);
    }

    public function testText()
    {
        $site = new Site();
        $site->title = 'Название сайта';

        $this->assertEquals('Название сайта', $site->title);
    }

    public function testEntityDecode()
    {
        $site = new Site();
        $site->title = 'Заголовок &quot;заголовок&quot;';

        $this->assertEquals('Заголовок "заголовок"', $site->title);
    }
}
