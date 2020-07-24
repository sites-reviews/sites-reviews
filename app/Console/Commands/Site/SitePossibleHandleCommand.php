<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\PossibleDomain;
use App\Site;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Litlife\Url\Url;
use Spatie\Browsershot\Browsershot;

class SitePossibleHandleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:possible_handle {count=10} {latest_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда обрабатывает таблицу с возможными доменами и пробует их добавить';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $client;
    private $count;
    private $latest_id;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $this->client = $client;
        $this->count = $this->argument('count');
        $this->latest_id = $this->argument('latest_id');

        PossibleDomain::query()
            ->unhandeled()
            ->where('id', '>=', $this->latest_id)
            ->chunkById($this->count, function ($domains) {
                foreach ($domains as $domain) {
                    $this->item($domain);
                }
            });
    }

    public function item(PossibleDomain $possibleDomain)
    {
        $site = new Site();
        $site->domain = $possibleDomain->domain;

        try
        {
            $available = $site->isAvailableThroughInternet($this->client);
        }
        catch (\Exception $exception)
        {
            Log::warning($exception->getMessage());
        }

        if (!empty($available))
        {
            $this->call('site:create', ['url' => $site->getUrl()]);
        }

        $possibleDomain->handeled_at = now();
        $possibleDomain->save();
    }
}
