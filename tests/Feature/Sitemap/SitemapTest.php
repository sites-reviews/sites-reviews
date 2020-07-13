<?php

namespace Tests\Feature\Sitemap;

use App\Site;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    private $storage;
    private $dirname;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dirname = 'sitemap';
        $this->storage = 'public';

        Storage::fake($this->storage);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUsers()
    {
        $user = factory(User::class)
            ->create();

        Artisan::call('sitemap:create', [
            'create_new' => true,
            'sendToSearchEngine' => false,
            'later_than_date' => $user->created_at,
            '--handle' => 'users',
            '--storage' => $this->storage,
            '--dirname' => $this->dirname
        ]);

        $files = Storage::disk($this->storage)->files($this->dirname);

        $content = Storage::disk($this->storage)->get($files[1]);

        $this->assertStringContainsString('<loc>'.route('users.show', $user).'</loc>', $content);
    }

    public function testSites()
    {
        $site = factory(Site::class)
            ->create();

        Artisan::call('sitemap:create', [
            'create_new' => true,
            'sendToSearchEngine' => false,
            'later_than_date' => $site->created_at,
            '--handle' => 'sites',
            '--storage' => $this->storage,
            '--dirname' => $this->dirname
        ]);

        $files = Storage::disk($this->storage)->files($this->dirname);

        $content = Storage::disk($this->storage)->get($files[1]);

        $this->assertStringContainsString('<loc>'.route('sites.show', $site).'</loc>', $content);
    }

    public function testRedirectWorks()
    {
        Storage::fake('public');

        $site = factory(Site::class)
            ->create();

        Artisan::call('sitemap:create', [
            'create_new' => true,
            'sendToSearchEngine' => false,
            'later_than_date' => $site->created_at,
            '--handle' => 'sites',
            '--storage' => $this->storage,
            '--dirname' => $this->dirname
        ]);

        $this->assertTrue(Storage::disk('public')->exists('sitemap/sitemap.xml'));

        $this->get(route('sitemap'))
            ->assertRedirect(Storage::disk('public')->url('sitemap/sitemap.xml'));
    }
}
