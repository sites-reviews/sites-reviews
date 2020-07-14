<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserSelectedLocaleAttributeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSet()
    {
        $user = new User();
        $user->selected_locale = 'ru';

        $this->assertEquals('ru', $user->selected_locale);
    }

    public function testToLowerTrim()
    {
        $user = new User();
        $user->selected_locale = '  RU';

        $this->assertEquals('ru', $user->selected_locale);
    }

    public function testNullIfLocaleNotInList()
    {
        $user = new User();
        $user->selected_locale = 'sdf';

        $this->assertEquals(null, $user->selected_locale);
    }
}
