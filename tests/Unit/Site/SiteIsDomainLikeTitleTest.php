<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteIsDomainLikeTitleTest extends TestCase
{
    public function test2()
    {
        $site = new Site();
        $site->title = 'Example.com';
        $site->domain = 'example.com';

        $this->assertTrue($site->isDomainLikeTitle());
    }

    public function test3()
    {
        $site = new Site();
        $site->title = 'Example2.com';
        $site->domain = 'example.com';

        $this->assertFalse($site->isDomainLikeTitle());
    }

    public function test4()
    {
        $site = new Site();
        $site->title = '   Example.COM';
        $site->domain = 'example.com';

        $this->assertTrue($site->isDomainLikeTitle());
    }

    public function test5()
    {
        $site = new Site();
        $site->title = 'www.Example.COM';
        $site->domain = 'example.com';

        $this->assertTrue($site->isDomainLikeTitle());
    }

    public function test6()
    {
        $site = new Site();
        $site->title = 'www.Example.COM';
        $site->domain = 'www.example.com';

        $this->assertTrue($site->isDomainLikeTitle());
    }

    public function test7()
    {
        $site = new Site();
        $site->title = 'www.Example.COM';
        $site->domain = 'www.example.com';

        $this->assertTrue($site->isDomainLikeTitle());
    }
}
