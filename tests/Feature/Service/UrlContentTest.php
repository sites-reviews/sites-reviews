<?php

namespace Tests\Feature\Service;

use App\Service\UrlContent;
use App\Site;
use Illuminate\Support\Str;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class UrlContentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOk()
    {
        $url = 'http://test.com';
        $content = uniqid();

        $response = new Response(200, [
            'Content-Type' => [
                'text/html; charset=UTF-8'
            ],
            'Transfer-Encoding' => [
                'chunked'
            ]
        ], $content);

        $mock = new MockHandler([
            $response
        ]);

        $handlerStack = HandlerStack::create($mock);

        $class = new UrlContent;

        $this->assertEquals($content, $class->getContent($url, $handlerStack));
    }
}
