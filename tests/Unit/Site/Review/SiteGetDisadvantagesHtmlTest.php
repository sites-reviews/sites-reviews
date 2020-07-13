<?php

namespace Tests\Unit\Site\Review;

use App\Review;
use App\Site;
use PHPUnit\Framework\TestCase;

class SiteGetDisadvantagesHtmlTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReplaceNewLinesToBr()
    {
        $review = new Review();
        $review->disadvantages = "test \n test";

        $this->assertEquals("test <br /> test", $review->getDisadvantagesHtml());
    }
}
