<?php

namespace Tests\Feature\Site\Verification;

use App\ProofOwnership;
use App\Service\DNS;
use App\Site;
use App\SiteOwner;
use App\User;
use Tests\TestCase;

class SiteVerificationCheckDnsTest extends TestCase
{
    public function testIfGuest()
    {
        $site = factory(Site::class)
            ->create();

        $response = $this->get(route('sites.verification.check.dns', $site))
            ->assertRedirect(route('login'));
    }

    public function testNoTxtRecordsWereFoundError()
    {
        $site = factory(Site::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $getRecordArray = collect([]);

        $this->mock(DNS::class, function ($mock) use ($getRecordArray) {
            $mock->shouldReceive('getRecord')
                ->once()
                ->andReturn($getRecordArray);
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.dns', $site))
            ->assertRedirect(route('sites.verification.request', $site))
            ->assertSessionHasErrors(['error' => __('No TXT records found')], null, 'check_dns');
    }

    public function testRequiredTxtRecordWasNotFoundError()
    {
        $site = factory(Site::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $collection = collect([
            [
                'host' => 'test.com',
                'class' => 'IN',
                "ttl" => 200,
                "type" => "TXT",
                "txt" => "v=spf1 ip4:1.1.1.1",
                "entries" => [
                    0 => "v=spf1 ip4:1.1.1.1"
                ]
            ],
            [
                'host' => 'test.com',
                'class' => 'IN',
                "ttl" => 200,
                "type" => "TXT",
                "txt" => "some-verification: 123",
                "entries" => [
                    0 => "some-verification: 123"
                ]
            ],
        ]);

        $this->mock(DNS::class, function ($mock) use ($collection) {
            $mock->shouldReceive('getRecord')
                ->once()
                ->andReturn($collection);
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.dns', $site))
            ->assertRedirect(route('sites.verification.request', $site))
            ->assertSessionHasErrors([
                'error' => __('No :dns_key_name TXT record found', ['dns_key_name' => config('verification.dns_key_name')])
            ], null, 'check_dns');
    }

    public function testTxtDoesNotMatchTheDesiredValueError()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $collection = collect([
            [
                'host' => 'test.com',
                'class' => 'IN',
                "ttl" => 200,
                "type" => "TXT",
                "txt" => "v=spf1 ip4:1.1.1.1",
                "entries" => [
                    0 => "v=spf1 ip4:1.1.1.1"
                ]
            ],
            [
                'host' => 'test.com',
                'class' => 'IN',
                "ttl" => 200,
                "type" => "TXT",
                "txt" => config('verification.dns_key_name') . ": 34234234234",
                "entries" => [
                    0 => config('verification.dns_key_name') . ": 34234234234"
                ]
            ],
        ]);

        $this->mock(DNS::class, function ($mock) use ($collection) {
            $mock->shouldReceive('getRecord')
                ->once()
                ->andReturn($collection);
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.dns', $site))
            ->assertRedirect()
            ->assertSessionHasErrors([
                'error' => __('TXT :dns_key_name does not match the desired value', ['dns_key_name' => config('verification.dns_key_name')])
            ], null, 'check_dns');

        $siteOwner->refresh();

        $this->assertTrue($siteOwner->isSentForReview());
    }

    public function testSuccessfullyConfirmed()
    {
        $siteOwner = factory(SiteOwner::class)
            ->states('not_confirmed')
            ->create();

        $proof = factory(ProofOwnership::class)
            ->create(['site_owner_id' => $siteOwner->id]);

        $site = $siteOwner->site;
        $user = $siteOwner->create_user;

        $this->assertTrue($siteOwner->isSentForReview());

        $collection = collect([
            [
                'host' => 'test.com',
                'class' => 'IN',
                "ttl" => 200,
                "type" => "TXT",
                "txt" => "v=spf1 ip4:1.1.1.1",
                "entries" => [
                    0 => "v=spf1 ip4:1.1.1.1"
                ]
            ],
            [
                'host' => 'test.com',
                'class' => 'IN',
                "ttl" => 200,
                "type" => "TXT",
                "txt" => config('verification.dns_key_name') . ": " . $proof->dns_code,
                "entries" => [
                    0 => config('verification.dns_key_name') . ": " . $proof->dns_code
                ]
            ],
        ]);

        $this->mock(DNS::class, function ($mock) use ($collection) {
            $mock->shouldReceive('getRecord')
                ->once()
                ->andReturn($collection);
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.dns', $site))
            ->assertRedirect()
            ->assertSessionHas(['success' => __("TXT record found.")." ".__("Verification completed")]);

        $siteOwner->refresh();
        $site->refresh();

        $this->assertTrue($siteOwner->isAccepted());
        $this->assertTrue($site->userOwner->is($user));
    }

    public function testDnsGetRecordATemporaryServerErrorOccurred()
    {
        $site = factory(Site::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $getRecordArray = collect([]);

        $this->mock(DNS::class, function ($mock) use ($getRecordArray) {
            $mock->shouldReceive('getRecord')
                ->once()
                ->andThrow(new \ErrorException('dns_get_record(): A temporary server error occurred.'));
        });

        $response = $this->actingAs($user)
            ->get(route('sites.verification.check.dns', $site))
            ->assertRedirect(route('sites.verification.request', $site))
            ->assertSessionHasErrors(['error' => __('A temporary server error occurred.')], null, 'check_dns');
    }
}
