<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteNumberOfAttemptsUpdateThePreviewAttributeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDefault()
    {
        $site = new Site();

        $this->assertEquals(0, $site->number_of_attempts_update_the_preview);
    }

    public function testSetDouble()
    {
        $site = new Site();
        $site->number_of_attempts_update_the_preview = '3.14';

        $this->assertEquals(3, $site->number_of_attempts_update_the_preview);
    }
}
