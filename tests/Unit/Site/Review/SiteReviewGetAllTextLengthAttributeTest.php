<?php

namespace Tests\Unit\Site\Review;

use App\Review;
use App\Site;
use PHPUnit\Framework\TestCase;

class SiteReviewGetAllTextLengthAttributeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDefault()
    {
        $review = new Review();

        $this->assertEquals(0, $review->getAllTextLength());
    }

    public function testWithoutEmpty()
    {
        $review = new Review();
        $review->advantages = 'g';
        $review->disadvantages = 'e';
        $review->comment = 't';

        $this->assertEquals(3, $review->getAllTextLength());
    }

    public function testWithEmptySpaces()
    {
        $review = new Review();
        $review->advantages = 'g    e';
        $review->disadvantages = 'e rt';
        $review->comment = 't     ва6п'."\n".'sdf';

        $this->assertEquals(13, $review->getAllTextLength());
    }
}
