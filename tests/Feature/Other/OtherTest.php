<?php

namespace Tests\Feature\Other;

use Tests\TestCase;

class OtherTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPersonalDataProcessingAgreement()
    {
        $this->get(route('personal_data_processing_agreement'))
            ->assertOk();
    }

    public function testPrivacyPolicy()
    {
        $this->get(route('privacy.policy'))
            ->assertOk();
    }
}
