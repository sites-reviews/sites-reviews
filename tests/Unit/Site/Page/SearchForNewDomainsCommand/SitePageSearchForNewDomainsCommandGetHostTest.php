<?php

namespace Tests\Unit\Site\Page\SearchForNewDomainsCommand;

use App\Console\Commands\Site\SitePageSearchForNewDomainsCommand;

class SitePageSearchForNewDomainsCommandGetHostTest extends \PHPUnit\Framework\TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test1()
    {
        $command = new SitePageSearchForNewDomainsCommand();

        $this->assertEquals('example.com', $command->getHost('https://www.example.com/test'));
    }

    public function test2()
    {
        $command = new SitePageSearchForNewDomainsCommand();

        $this->assertEquals(false, $command->getHost('https://host/test'));
    }

    public function test3()
    {
        $command = new SitePageSearchForNewDomainsCommand();

        $this->assertEquals(false, $command->getHost('host/test'));
    }

    public function test4()
    {
        $command = new SitePageSearchForNewDomainsCommand();

        $this->assertEquals('example.com', $command->getHost('//example.com/test'));
    }

    public function test5()
    {
        $command = new SitePageSearchForNewDomainsCommand();

        $this->assertEquals(false, $command->getHost('://example.com/test'));
    }

    public function test6()
    {
        $command = new SitePageSearchForNewDomainsCommand();

        $this->assertEquals(false, $command->getHost(''));
    }

    public function test7()
    {
        $command = new SitePageSearchForNewDomainsCommand();

        $this->assertEquals(false, $command->getHost('test'));
    }
}
