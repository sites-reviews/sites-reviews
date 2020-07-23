<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\PossibleDomain;
use App\Site;
use App\SitePage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Litlife\Url\Url;
use Spatie\Browsershot\Browsershot;

class SitePageSearchForNewDomainsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site_page:search_for_new_domains {count=10} {latest_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда парсит контент страниц и пытается найти и добавить новые домены';

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
        $count = $this->argument('count');
        $latestId = $this->argument('latest_id');

        SitePage::query()
            ->where('id', '>=', $latestId)
            ->whereNull('search_for_new_domains_is_completed_at')
            ->chunkById(10, function ($pages) {
            foreach ($pages as $page) {
                try {
                    $this->item($page);
                } catch (\Exception $exception) {
                    report($exception);
                    $this->error($exception->getMessage());
                }

                $page->search_for_new_domains_is_completed_at = now();
                $page->save();
            }
        });

        return true;
    }

    public function item(SitePage $page)
    {
        $hosts = [];

        foreach ($page->xpath()->query('//a') as $node)
        {
            if ($node->hasAttribute('href'))
            {
                $href = $node->getAttribute('href');

                if ($host = $this->getHost($href))
                {
                    $hosts[] = $host;
                }
            }
        }

        $hosts = array_unique($hosts);

        foreach ($hosts as $host)
        {
            $this->host($host);
        }
    }

    public function host(string $host)
    {
        if (Site::whereDomain($host)->first())
            return false;

        if (PossibleDomain::whereDomain($host)->first())
            return false;

        $possibleDomain = new PossibleDomain();
        $possibleDomain->domain = $host;
        $possibleDomain->save();
    }

    public function getHost(string $url)
    {
        try {
            $url = Url::fromString($url);
        } catch (\Exception $exception) {
            return false;
        }

        $host = trim($url->getHost());

        if (preg_match('/\./iu', $host))
        {
            if (preg_match('/^(?:www\.)(.*)$/iu', $host, $matches))
            {
                $host = $matches[1];
            }

            if (preg_match('/^([1-9]+)\.([1-9]+)\.([1-9]+)\.([1-9]+)$/iu', $host))
                return false;

            if (inet_pton($host))
                return false;

            return trim($host);
        }

        return false;
    }
}
