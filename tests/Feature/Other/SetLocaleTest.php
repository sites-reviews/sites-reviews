<?php

namespace Tests\Feature\Other;

use App\Site;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class SetLocaleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
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

    public function testLocaleSavedIntoCookies()
    {
        $site = factory(Site::class)->create();

        $this->get(route('sites.show', ['locale' => 'ru', 'site' => $site, 'test' => 'test']))
            ->assertOk();

        $this->get(route('locale.set', ['set_locale' => 'en']))
            ->assertRedirect(route('sites.show', ['locale' => 'en', 'site' => $site, 'test' => 'test']))
            ->assertCookie('locale', 'en');
    }

    public function testSavedIntoCookiesAndIfPreviousRouteNotFound()
    {
        $url = '/'.mb_strtolower(Str::random());

        $this->get(route('locale.set', ['set_locale' => 'en']), ['HTTP_REFERER' => $url])
            ->assertRedirect(route('home'))
            ->assertCookieMissing('locale');
    }
}
