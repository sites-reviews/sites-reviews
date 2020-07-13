<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteRatingForButtonTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test5()
    {
        $site = new Site();
        $site->rating = 5;

        $this->assertEquals(5.0, $site->getRatingForButton());
    }

    public function test43()
    {
        $site = new Site();
        $site->rating = 4.3;

        $this->assertEquals(4.3, $site->getRatingForButton());
    }

    public function test2()
    {
        $site = new Site();
        $site->rating = 2;

        $this->assertEquals(2.0, $site->getRatingForButton());
    }
}
