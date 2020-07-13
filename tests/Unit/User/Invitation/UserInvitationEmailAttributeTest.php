<?php

namespace Tests\Unit\User\Invitation;

use App\UserInvitation;
use PHPUnit\Framework\TestCase;

class UserInvitationEmailAttributeTest extends TestCase
{
    public function testTrim()
    {
        $invitation = new UserInvitation();
        $invitation->email = '  test@test.test';

        $this->assertEquals('test@test.test', $invitation->email);
    }

    public function testStrToLower()
    {
        $invitation = new UserInvitation();
        $invitation->email = 'Test@teSt.TEST';

        $this->assertEquals('test@test.test', $invitation->email);
    }
}
