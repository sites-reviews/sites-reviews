<?php

namespace Tests\Feature\Site\Verification;

use App\Site;
use App\User;
use Tests\TestCase;

class SiteVerificationRequestTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRequest()
    {
        $site = factory(Site::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->get(route('sites.verification.request', $site))
            ->assertOk();

        $siteOwner = $site->siteOwners()->first();

        $this->assertNotNull($siteOwner);
        $this->assertEquals($site->id, $siteOwner->site_id);
        $this->assertEquals($user->id, $siteOwner->create_user_id);
        $this->assertTrue($siteOwner->isSentForReview());

        $proof = $siteOwner->proof()->first();

        $this->assertNotNull($proof);
        $this->assertEquals($site->id, $siteOwner->site_id);
        $this->assertEquals($user->id, $siteOwner->create_user_id);
        $this->assertNotNull($proof->dns_code);
        $this->assertNotNull($proof->file_path);
        $this->assertNotNull($proof->file_code);

        $userOwner = $site->userOwner;

        $this->actingAs($user)
            ->get(route('sites.verification.request', $site))
            ->assertOk()
            ->assertViewHas('userOwner', $userOwner);
    }

    public function testIfAuthUserIsAlreadyVerfified()
    {
        $site = factory(Site::class)
            ->states('with_owner')
            ->create();

        $userOwner = $site->userOwner;

        $this->assertNotNull($userOwner);

        $this->actingAs($userOwner)
            ->get(route('sites.verification.request', $site))
            ->assertOk()
            ->assertViewHas('userOwner', $userOwner)
            ->assertSeeText(__('Verification completed'));
    }
}
