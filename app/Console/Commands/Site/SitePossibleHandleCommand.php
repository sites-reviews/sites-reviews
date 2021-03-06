<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\PossibleDomain;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;
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
            ->limit($this->count)
            ->get()
            ->each(function ($domain) {
                $this->item($domain);
            });
    }

    public function item(PossibleDomain $possibleDomain)
    {
        try {
            $site = new Site();
            $site->domain = $possibleDomain->domain;

            if ($site->isAvailableThroughInternet($this->client)) {
                $this->call('site:create', ['url' => $site->getUrl()]);
            }

        } catch (\LogicException $exception) {
           if ($exception->getMessage() == 'The domain cannot be empty')
           {
               $possibleDomain->handeled_at = now();
           } else {
               throw $exception;
           }
        }

        $possibleDomain->handeled_at = now();
        $possibleDomain->save();
    }
}
