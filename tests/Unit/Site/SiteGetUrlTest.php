<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteGetUrlTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test()
    {
        $site = new Site();
        $site->domain = 'test.com';

        $this->assertEquals('http://test.com', $site->getUrl());
    }
}
