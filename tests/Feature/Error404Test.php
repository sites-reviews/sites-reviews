<?php

namespace Tests\Feature;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class Error404Test extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRedirectToDefaultLocale()
    {
        $str = Str::random(12);

        $response = $this->get('/'.$str.'/?test=test', ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'])
            ->assertRedirect('/'.Config::get('app.locale').'/'.$str.'/?test=test');
    }

    public function test404Status()
    {
        $str = Str::random(12);

        $this->get('/'.Config::get('app.locale').'/'.$str, ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'])
            ->assertNotFound();
    }

    public function testRedirectToSavedLocaleIfUserAuth()
    {
        \Config::set('app.locale', 'ru');

        $user = factory(User::class)
            ->create(['selected_locale' => 'en']);

        $response = $this->actingAs($user)
            ->get('/')
            ->assertRedirect('/en');
    }

    public function testNotRedirectToSavedLocaleIfUserAuth()
    {
        \Config::set('app.locale', 'ru');

        $user = factory(User::class)
            ->create(['selected_locale' => null]);

        $response = $this->actingAs($user)
            ->get('/', ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'])
            ->assertRedirect('/ru');
    }

    public function testRedirectToLangUsingHttpAcceptLanguage()
    {
        \Config::set('app.locale', 'en');

        \Config::set('app.locales', ['en', 'ru', 'es']);

        $response = $this->get('/', ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'])
            ->assertRedirect('/ru');
    }

    public function testRedirectToLangUsingHttpAcceptLanguage2()
    {
        \Config::set('app.locale', 'en');

        \Config::set('app.locales', ['ru', 'es']);

        $response = $this->get('/', ['HTTP_ACCEPT_LANGUAGE' => 'uk-UK,uk;q=0.9,en-US;q=0.8,es;q=0.7'])
            ->assertRedirect('/es');
    }

    public function testRedirectToSavedLocaleInCookies()
    {
        $response = $this->withCookie('locale', 'ru')
            ->get('/')
            ->assertRedirect('/ru');

        $response = $this->withCookie('locale', 'en')
            ->get('/')
            ->assertRedirect('/en');
    }
}
