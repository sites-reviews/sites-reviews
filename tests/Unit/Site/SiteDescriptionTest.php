<?php

namespace Tests\Unit\Site;

use App\Site;
use PHPUnit\Framework\TestCase;

class SiteDescriptionTest extends TestCase
{
    public function testTrim()
    {
        $site = new Site();
        $site->description = ' Описание ';

        $this->assertEquals('Описание', $site->description);
    }

    public function testEntityDecode()
    {
        $site = new Site();
        $site->description = 'Описание &quot;описание&quot;';

        $this->assertEquals('Описание "описание"', $site->description);
    }
}
