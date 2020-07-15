<?php

namespace Tests\Unit;

use App\Service\UrlContent;
use PHPUnit\Framework\TestCase;

class UrlContentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetContent()
    {
        $class = new UrlContent();

        $this->assertNotNull($class->getContent('https://example.com'));
    }
}
