<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteRatingAttributeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testValue()
    {
        $site = new Site();
        $site->rating = 5;

        $this->assertEquals(5, $site->rating);
    }

    public function testRound()
    {
        $site = new Site();
        $site->rating = 3.25678956;

        $this->assertEquals(3.26, $site->rating);

        $site->rating = 3.25378956;

        $this->assertEquals(3.25, $site->rating);
    }

    public function testDefault()
    {
        $site = new Site();

        $this->assertEquals(0, $site->rating);
    }

    public function testMin()
    {
        $site = new Site();
        $site->rating = -2;

        $this->assertEquals(0, $site->rating);
    }

    public function testMax()
    {
        $site = new Site();
        $site->rating = 145;

        $this->assertEquals(5, $site->rating);
    }
}
