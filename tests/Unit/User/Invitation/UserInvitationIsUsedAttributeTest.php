<?php

namespace Tests\Unit\User\Invitation;

use App\UserInvitation;
use PHPUnit\Framework\TestCase;

class UserInvitationIsUsedAttributeTest extends TestCase
{
    public function testTrue()
    {
        $invitation = new UserInvitation();
        $invitation->used_at = now();

        $this->assertTrue($invitation->isUsed());
    }

    public function testFalse()
    {
        $invitation = new UserInvitation();
        $invitation->used_at = null;

        $this->assertFalse($invitation->isUsed());
    }
}
