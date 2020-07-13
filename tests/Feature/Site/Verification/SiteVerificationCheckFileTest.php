<?php

namespace Tests\Feature\Site\Verification;

use App\ProofOwnership;
use App\Site;
use App\SiteOwner;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Tests\TestCase;
use GuzzleHttp\Psr7;

class SiteVerificationCheckFileTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUnknownException()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $this->mock(Client::class, function ($mock) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow(new \Exception('test', 1));
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.file', $site))
            ->assertRedirect(route('sites.verification.request', $site))
            ->assertSessionHasErrors([
                'error' => __('verification.error_when_checking_verification_using_a_file')
            ], null,'check_file');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testConnectException()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $request = new Psr7\Request('get', '');

        $connectException = new ConnectException('', $request, null, [
            'errno' => 6,
            'error' => 'Could not resolve host'
        ]);

        $this->mock(Client::class, function ($mock) use ($connectException) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow($connectException);
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.file', $site))
            ->assertRedirect(route('sites.verification.request', $site))
            ->assertSessionHasErrors(['error' => __('verification.error_connecting_to_the_site', [
                'errno' => 6,
                'error_text' => 'Could not resolve host'
            ])], null,'check_file');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testClientException()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $response = new Response(501, [], null, '1.1', 'Some Error');

        $request = new Psr7\Request('get', '');

        $clientException = new ClientException('', $request, $response);

        $this->mock(Client::class, function ($mock) use ($clientException) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow($clientException);
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.file', $site))
            ->assertRedirect(route('sites.verification.request', $site))
            ->assertSessionHasErrors(['error' => __('verification.error_connecting_to_the_site_server', [
                'status_code' => 501,
                'reason_phrase' => 'Some Error'
            ])], null,'check_file');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFileWasNotFound()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $response = new Response(404, [], null, '1.1', 'Not Found');

        $request = new Psr7\Request('get', '');

        $clientException = new ClientException('', $request, $response);

        $this->mock(Client::class, function ($mock) use ($clientException) {
            $mock->shouldReceive('request')
                ->once()
                ->andThrow($clientException);
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.file', $site))
            ->assertRedirect(route('sites.verification.request', $site))
            ->assertSessionHasErrors(['error' => __('verification.the_file_with_the_required_name_was_not_found')], null,'check_file');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFileContentDoesNotMatchTheDesiredContent()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $stream = Psr7\stream_for('test');

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

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.file', $site))
            ->assertRedirect(route('sites.verification.request', $site));
//var_dump(session('errors'));
        $response->assertSessionHasErrors(['error' => __('verification.the_file_content_does_not_match_the_desired_content')], null,'check_file');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFileContentMatchTheDesiredContent()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $stream = Psr7\stream_for($proof->file_code);

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

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.file', $site))
            ->assertRedirect(route('sites.verification.request', $site));

        $response->assertSessionHas(['success' => __('verification.the_file_with_the_desired_content_is_found_on_the_site_verification_completed')]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testARedirectOccurredInsteadOfTheFileError()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $stream = Psr7\stream_for($proof->file_code);

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

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.file', $site))
            ->assertRedirect(route('sites.verification.request', $site));

        $response->assertSessionHasErrors(['error' => __('verification.a_redirect_occurred_instead_of_the_file')], null,'check_file');
    }
}
