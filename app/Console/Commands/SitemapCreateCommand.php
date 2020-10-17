<?php

namespace App\Console\Commands;

use App\Site;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Litlife\Sitemap\Sitemap;
use Litlife\Sitemap\SitemapIndex;
use Litlife\Url\Url;

class SitemapCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:create 
                                 {create_new=true} 
                                 {sendToSearchEngine=false}
                                 {later_than_date?}
                                 {--handle=all}
                                 {--storage=public}
                                 {--dirname=sitemap}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать карту сайта';
    private $storage;
    private $dirname;
    private $sitemapIndex;
    private $currentSitemap;
    private $olderThanDate;
    private $createNew = true;
    private $currentSitemapName;
    private $locales;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dirname = $this->option('dirname');
        $this->storage = $this->option('storage');

        $this->createNew = $this->argument('create_new');

        $this->locales = config('app.locales');

        if (!empty($this->argument('later_than_date')))
            $this->olderThanDate = Carbon::parse($this->argument('later_than_date'));

        if ($this->createNew) {
            $this->clearDirectory();
            $this->createSitemapIndex();
            $this->createSitemap();

        } else {

            $content = Storage::disk($this->storage)
                ->get($this->dirname . '/sitemap.xml');

            $this->sitemapIndex = new SitemapIndex();
            $this->sitemapIndex->open($content);

            $lastSitemap = $this->sitemapIndex->getLastSitemap();

            $basename = Url::fromString($lastSitemap['location'])
                ->getBasename();

            $content = Storage::disk($this->storage)
                ->get($this->dirname . '/' . $basename);

            $this->currentSitemap = new Sitemap();
            $this->currentSitemap->open($content);
            $this->currentSitemapName = $basename;
        }

        $handle = $this->option('handle');

        if ($handle == 'all') {
            $this->home();
            $this->sites();
            $this->users();
        } else {
            $handleArray = explode(',', $handle);

            foreach ($handleArray as $name) {
                $this->{$name}();
            }
        }

        $this->saveSitemapIndexToFile();

        if ($this->argument('sendToSearchEngine'))
            $this->sendToSearchEngine();

        return true;
    }

    private function clearDirectory()
    {
        if (Storage::disk($this->storage)->exists($this->dirname)) {
            foreach (Storage::disk($this->storage)->allFiles($this->dirname) as $file) {
                if (!Storage::disk($this->storage)->delete($file)) {
                    throw new \Exception('Файл не удален ' . $file);
                }
            }

        } else {
            Storage::disk($this->storage)
                ->makeDirectory($this->dirname);
        }
    }

    public function createSitemapIndex()
    {
        $this->sitemapIndex = new SitemapIndex();
    }

    public function createSitemap()
    {
        $this->currentSitemap = new Sitemap();
    }

    public function home()
    {
        $this->info('home');

        foreach ($this->locales as $locale) {
            $this->addUrl(
                route('home', ['locale' => $locale]),
                now(), 'weekly', 0.6
            );
        }
    }

    public function addUrl($location, $lastmod = null, $changefreq = 'weekly', $priority = '0.5')
    {
        $this->getCurrentSitemap()->addUrl($location, $lastmod, $changefreq, $priority);

        if ($this->getCurrentSitemap()->isCountOfURLsIsGreaterOrEqualsThanMax() or $this->getCurrentSitemap()->isSizeLargerOrEqualsThanMax()) {
            $this->saveCurrentSitemap();

            $this->currentSitemap = new Sitemap();
        }
    }

    public function getCurrentSitemap(): Sitemap
    {
        return $this->currentSitemap;
    }

    public function saveCurrentSitemap()
    {
        if ($this->createNew)
            $path = $this->generateNewName();
        else
            $path = $this->dirname . '/' . $this->currentSitemapName;

        Storage::disk($this->storage)
            ->put(
                $path,
                $this->getCurrentSitemap()->getContent()
            );

        if (isset($this->output))
            $this->info("\n" . 'File ' . $path . ' created');

        $this->sitemapIndex->addSitemap(
            Storage::disk($this->storage)->url($path),
            now());
    }

    public function generateNewName()
    {
        $fileName = 'sitemap_' . Carbon::now()->format('Y-m-d_H_i_s') . '.xml';

        return $this->dirname . '/' . $fileName;
    }

    public function sites()
    {
        $this->info('sites');

        $bar = $this->output->createProgressBar(Site::count());

        $bar->start();

        Site::query()
            ->when(!empty($this->olderThanDate), function ($query) {
                $query->where('created_at', '>=', $this->olderThanDate);
            })
            ->chunkById(1000, function ($items) use ($bar) {
                foreach ($items as $item) {
                    $this->site($item);
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->info('');
    }

    public function site(Site $site)
    {
        foreach ($this->locales as $locale) {
            if (!empty($site->domain))
            {
                $this->addUrl(
                    route('sites.show', ['site' => $site, 'locale' => $locale]),
                    $site->updated_at, 'weekly', 0.6
                );
            }
        }
    }

    public function users()
    {
        $this->info('users');

        $bar = $this->output->createProgressBar(User::count());

        $bar->start();

        User::query()
            ->when(!empty($this->olderThanDate), function ($query) {
                $query->where('created_at', '>=', $this->olderThanDate);
            })
            ->chunkById(1000, function ($items) use ($bar) {
                foreach ($items as $item) {
                    $this->user($item);
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->info('');
    }

    public function user(User $user)
    {
        foreach ($this->locales as $locale)
        {
            $this->addUrl(
                route('users.show', ['user' => $user, 'locale' => $locale]),
                $user->updated_at, 'weekly', 0.6
            );
        }
    }

    public function saveSitemapIndexToFile()
    {
        $this->saveCurrentSitemap();

        if (Storage::disk($this->storage)->exists($this->dirname . '/sitemap.xml'))
            Storage::disk($this->storage)->delete($this->dirname . '/sitemap.xml');

        Storage::disk($this->storage)
            ->put($this->dirname . '/sitemap.xml', $this->sitemapIndex->getContent());
    }

    public function sendToSearchEngine()
    {
        $host = Url::fromString(config('app.url'))->getHost();

        $this->ping((string)Url::fromString('http://google.com/ping?sitemap=https://' . $host . '/sitemap.xml'));
        $this->ping((string)Url::fromString('http://webmaster.yandex.ru/ping?sitemap=https://' . $host . '/sitemap.xml'));
    }

    public function ping($url)
    {
        $client = new Client();

        $options = config('guzzle.request.options');

        $response = $client->request('GET', $url, $options)
            ->getBody();
    }

    public function getSitemapIndex()
    {
        return $this->sitemapIndex;
    }
}
