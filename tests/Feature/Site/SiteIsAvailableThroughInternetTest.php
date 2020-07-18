<?php

namespace Tests\Feature\Site;

use App\Review;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;

class SiteIsAvailableThroughInternetTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOk()
    {
        $site = factory(Site::class)
            ->create(['domain' => 'example.com']);

        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], 'Hello, World')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->assertTrue($site->isAvailableThroughInternet($client));
    }
}
