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
}
