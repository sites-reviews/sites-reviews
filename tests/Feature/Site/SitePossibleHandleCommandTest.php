<?php

namespace Tests\Feature\Site;

use App\Console\Commands\Site\SitePossibleHandleCommand;
use App\PossibleDomain;
use App\Review;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7;

class SitePossibleHandleCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteAvailable()
    {
        $possibleDomain = factory(PossibleDomain::class)
            ->create();

        $this->assertNull($possibleDomain->handeled_at);

        $stream = Psr7\stream_for('');

        $response = new Response(200, [
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

        $this->artisan('site:possible_handle', ['count' => '1', 'latest_id' => $possibleDomain->id]);

        $possibleDomain->refresh();

        $this->assertNotNull($possibleDomain->handeled_at);

        $site = Site::whereDomain($possibleDomain->domain)->first();

        $this->assertNotNull($site);
    }

    public function testSiteNotAvailable()
    {
        $possibleDomain = factory(PossibleDomain::class)
            ->create();

        $this->assertNull($possibleDomain->handeled_at);

        $stream = Psr7\stream_for('');

        $response = new Response(404, [
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

        $this->artisan('site:possible_handle', ['count' => '1', 'latest_id' => $possibleDomain->id]);

        $possibleDomain->refresh();

        $this->assertNotNull($possibleDomain->handeled_at);

        $site = Site::whereDomain($possibleDomain->domain)->first();

        $this->assertNull($site);
    }

    public function testSiteHandeled()
    {
        $possibleDomain = factory(PossibleDomain::class)
            ->create(['handeled_at' => now()]);

        $this->mock(Client::class, function ($mock) {
            $mock->shouldNotReceive('request');
        });

        $this->artisan('site:possible_handle', ['count' => '1', 'latest_id' => $possibleDomain->id]);
    }
}
