<?php

namespace Tests\Unit\Site\Page\SiteUpdateContentCommand;

use App\Console\Commands\Site\SiteUpdateContentCommand;
use App\Site;
use App\SitePage;
use PHPUnit\Framework\TestCase;

class ParseEncodingFromHeaderFunctionTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testParseUtf8()
    {
        $command = new SiteUpdateContentCommand();

        $this->assertEquals('utf-8', $command->parseEncodingFromHeader('text/html; charset=UTF-8'));
    }

    public function testParseCp1251()
    {
        $command = new SiteUpdateContentCommand();

        $this->assertEquals('cp1251', $command->parseEncodingFromHeader('text/html; charset=cp1251'));
    }

    public function testParseWindows1251()
    {
        $command = new SiteUpdateContentCommand();

        $this->assertEquals('windows-1251', $command->parseEncodingFromHeader('text/html; charset=WINdows-1251'));
    }

    public function testParseWithNoEncoding()
    {
        $command = new SiteUpdateContentCommand();

        $this->assertEquals(false, $command->parseEncodingFromHeader('text/html;'));
    }
}
