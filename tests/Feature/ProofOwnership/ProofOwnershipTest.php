<?php

namespace Tests\Feature\ProofOwnership;

use App\ProofOwnership;
use Tests\TestCase;

class ProofOwnershipTest extends TestCase
{
    public function testVerificationCodes()
    {
        $proof = factory(ProofOwnership::class)
            ->create();

        $this->assertRegExp('/([a-z0-9]{32})/iu', $proof->dns_code);
        $this->assertRegExp('/'.config('verification.dns_key_name').'\-([a-z0-9]{32})\.txt/iu', $proof->file_path);
        $this->assertRegExp('/([a-z0-9]{32})/iu', $proof->file_code);
    }

    public function testGetFileUrl()
    {
        $proof = factory(ProofOwnership::class)
            ->create();

        $site = $proof->siteOwner->site;

        $this->assertEquals('http://'.$site->domain.'/'.$proof->file_path, $proof->getFileUrl());
    }
}
