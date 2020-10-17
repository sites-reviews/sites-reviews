<?php

namespace Tests\Feature\Site;

use App\Enums\SiteHowAddedEnum;
use App\Review;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7;

class SiteCreateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIfSiteNotCreated()
    {
        $domain = mb_strtolower(Str::random(8)) . $this->faker->domainName;

        $stream = Psr7\stream_for('');

        $response = new Response(302, [
            'Content-Type' => [
                'text/html; charset=UTF-8'
            ],
            'Transfer-Encoding' => [
                'chunked'
            ]
        ], $stream);

        $this->mock(Client::class, function ($mock) use ($response) {
            $mock->shouldReceive('request')
                ->once()
                ->andReturn($response);
        });

        $response = $this->get(route('sites.create.or_show', ['domain' => $domain]))
            ->assertRedirect()
            ->assertSessionHas('site_created', true);

        $site = Site::whereDomain($domain)->first();

        $this->assertNotNull($site);
        $this->assertEquals(mb_strtolower($domain), $site->domain);
        $this->assertEquals(mb_ucfirst($domain), $site->title);
        $this->assertTrue($site->update_the_preview);
        $this->assertTrue($site->update_the_page);
        $this->assertEquals(SiteHowAddedEnum::Manually, $site->how_added);

        $response->assertRedirect(route('sites.show', $site));
    }

    public function testIfSiteAlreadyCreated()
    {
        $site = factory(Site::class)->create();

        $response = $this->get(route('sites.create.or_show', ['domain' => $site->domain]))
            ->assertRedirect(route('sites.show', $site))
            ->assertSessionHas('site_exists', true);
    }

    public function test406Error()
    {
        $domain = Str::random(8) . $this->faker->domainName;

        $request = new Psr7\Request('get', '');

        $response = new Psr7\Response(406, [], '');

        $message = "Client error: `GET http://skyfitnessng.com` resulted in a `406 Not Acceptable` response:" .
            "<html><head><title>Error 406 - Not Acceptable</title><head><body><h1>Error 406 - Not Acceptable</h1><p>Generally a 406 e (truncated...)";

        $connectException = new ClientException($message, $request, $response);

        $this->mock(Client::class, function ($mock) use ($connectException) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow($connectException);
        });

        $this->get(route('home'))
            ->assertOk();

        $response = $this->get(route('sites.create.or_show', ['domain' => $domain]))
            ->assertRedirect(route('sites.search', ['term' => $domain]))
            ->assertSessionHasErrors(['error' =>
                __("Error adding a site").'. '.
                __('The server responded: ":code :phrase"', ['phrase' => $response->getReasonPhrase(), 'code' => $response->getStatusCode()])
            ], '', 'create_site');
    }

    public function testConnectException()
    {
        $domain = Str::random(8) . $this->faker->domainName;

        $request = new Psr7\Request('get', '');

        $context = [
            'errno' => '28',
            'error' => 'Resolving timed out after 5514 milliseconds'
        ];

        $connectException = new ConnectException(
            'cURL error 28: Resolving timed out after 5514 milliseconds (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)',
            $request, null, $context);

        $this->mock(Client::class, function ($mock) use ($connectException) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow($connectException);
        });

        $this->get(route('home'))
            ->assertOk();

        $response = $this->get(route('sites.create.or_show', ['domain' => $domain]))
            ->assertRedirect(route('sites.search', ['term' => $domain]))
            ->assertSessionHasErrors(['error' =>
                __("Error connecting to the site \":code :phrase\"", [
                    'phrase' => $context['error'],
                    'code' => $context['errno'],
                ])
            ], '', 'create_site');
    }
}
