<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteNumberOfAttemptsUpdateThePageAttributeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDefault()
    {
        $site = new Site();

        $this->assertEquals(0, $site->number_of_attempts_update_the_page);
    }

    public function testSetDouble()
    {
        $site = new Site();
        $site->number_of_attempts_update_the_page = '3.14';

        $this->assertEquals(3, $site->number_of_attempts_update_the_page);
    }
}
