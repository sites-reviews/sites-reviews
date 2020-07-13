<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPasswordAttributeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHashed()
    {
        $password = '123';

        $user = new User();
        $user->password = $password;

        $this->assertEquals(60, mb_strlen($user->password));
    }
}
