<?php

namespace Tests\Feature\Other;

use App\User;
use Tests\TestCase;

class SetLocaleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOk()
    {
        $this->get(route('locale.set', ['locale' => 'en']))
            ->assertSessionHas(['locale' => 'en'])
            ->assertRedirect();
    }

    public function testNotDefinedLocale()
    {
        $this->get(route('locale.set', ['locale' => 'aaa']))
            ->assertSessionMissing(['locale' => 'aaa'])
            ->assertRedirect();
    }

    public function testSetLocaleFromSession()
    {
        \Config::set('app.locales', ['en', 'ru', 'es']);

        \App::setLocale('ru');

        $this->withSession(['locale' => 'es'])
            ->get(route('home'))
            ->assertOk();

        $this->assertEquals('es', \App::getLocale());
    }

    public function testLocaleList()
    {
        $array = config('app.local_flag_map');

        $this->get(route('locale.list'))
            ->assertOk()
            ->assertViewHas('locale', [
                'ru' => 'ru',
                'en' => 'gb'
            ]);
    }

    public function testRememberLocaleIfAuth()
    {
        $user = factory(User::class)->create();

        \Config::set('app.locales', ['en', 'ru', 'es']);

        $this->assertNull($user->selected_locale);

        $this->actingAs($user)
            ->get(route('locale.set', ['locale' => 'en']))
            ->assertSessionHas(['locale' => 'en'])
            ->assertRedirect();

        $user->refresh();

        $this->assertEquals('en', $user->selected_locale);
    }

    public function testLocaleSavedIntoCookies()
    {
        $this->get(route('locale.set', ['locale' => 'en']))
            ->assertRedirect()
            ->assertSessionHas(['locale' => 'en'])
            ->assertCookie('locale', 'en');
    }

    public function testRestoreLocaleToSessionIfCookieExists()
    {
        $this->withCookie('locale', 'en')
            ->get(route('home'))
            ->assertOk()
            ->assertSessionHas(['locale' => 'en']);
    }
}
