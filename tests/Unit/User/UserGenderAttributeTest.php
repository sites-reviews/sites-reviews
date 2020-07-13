<?php

namespace Tests\Unit\User;

use App\User;
use PHPUnit\Framework\TestCase;

class UserGenderAttributeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDefault()
    {
        $user = new User();

        $this->assertEquals('unknown', $user->gender);
    }

    public function testMale()
    {
        $user = new User();
        $user->gender = 'male';

        $this->assertEquals('male', $user->gender);
    }

    public function testFemale()
    {
        $user = new User();
        $user->gender = 'female';

        $this->assertEquals('female', $user->gender);
    }
}
