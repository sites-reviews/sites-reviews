<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteDomainTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testToLower()
    {
        $site = new Site();
        $site->domain = 'Test.com';

        $this->assertEquals('test.com', $site->domain);
    }

    public function testTrim()
    {
        $site = new Site();
        $site->domain = '   test.com    ';

        $this->assertEquals('test.com', $site->domain);
    }

    public function testRemoveWWW()
    {
        $site = new Site();
        $site->domain = '   www.test.com    ';

        $this->assertEquals('test.com', $site->domain);
    }

    public function testDontRemoveW()
    {
        $site = new Site();
        $site->domain = 'webtest.com';

        $this->assertEquals('webtest.com', $site->domain);
    }

    public function testTrimDots()
    {
        $site = new Site();
        $site->domain = '..webtest.com..';

        $this->assertEquals('webtest.com', $site->domain);
    }
}
