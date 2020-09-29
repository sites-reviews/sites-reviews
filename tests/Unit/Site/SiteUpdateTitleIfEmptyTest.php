<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteUpdateTitleIfEmptyTest extends TestCase
{
    public function testDontUpdateIfEmpty()
    {
        $site = new Site();
        $site->title = 'Test2';
        $site->domain = 'example.com';
        $site->updateTitleIfEmpty();

        $this->assertEquals('Test2', $site->title);
    }

    public function testUpdateIfEmpty()
    {
        $site = new Site();
        $site->title = '';
        $site->domain = 'example.com';
        $site->updateTitleIfEmpty();

        $this->assertEquals('Example.com', $site->title);
    }
}
