<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteNumberOfReviewsHumanReadableTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test500()
    {
        $site = new Site();
        $site->number_of_reviews = 500;

        $this->assertEquals(500, $site->getNumberOfReviewsHumanReadable());
    }

    public function test1100()
    {
        $site = new Site();
        $site->number_of_reviews = 1100;

        $this->assertEquals('1.1K', $site->getNumberOfReviewsHumanReadable());
    }

    public function test124570()
    {
        $site = new Site();
        $site->number_of_reviews = 124570;

        $this->assertEquals('124.6K', $site->getNumberOfReviewsHumanReadable());
    }

    public function test10000000()
    {
        $site = new Site();
        $site->number_of_reviews = 10000000;

        $this->assertEquals('10M', $site->getNumberOfReviewsHumanReadable());
    }
}
