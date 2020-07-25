<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\Service\UrlContent;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Litlife\Url\Url;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class SiteUpdateContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:update_content {site_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда обновляет содержимое сайта';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $site;

    /**
     * Execute the console command.
     *
     * @param Client $client
     * @return mixed
     */
    public function handle(Client $client)
    {
        $this->site = Site::findOrFail($this->argument('site_id'));

        try {
            $response = $this->getResponse($client, $this->site->getUrl());

            $content = $response->getBody()
                ->getContents();

            if (empty($encoding)) {
                $encoding = $this->parseEncodingFromHtml($content);
            }

            if (empty($encoding)) {
                $headers = $response->getHeaders();

                if (!empty($headers['Content-Type'])) {
                    if (is_array($headers['Content-Type']))
                        $header = pos($headers['Content-Type']);
                    else
                        $header = $headers['Content-Type'];

                    $encoding = $this->parseEncodingFromHeader($header);
                }
            }

            if ($encoding != 'utf-8') {
                if (!empty($encoding)) {
                    $content = $this->convertToUtf8Encoding($content, $encoding);
                }
            }

            $this->site->page->content = $content;
            $this->site->updateDescriptionFromPage();
            $this->site->number_of_attempts_update_the_page = 0;
            $this->site->push();

            $this->info(__('Site content was updated successfully'));

            return true;

        } catch (ConnectException $exception) {
            return $this->failedAttempt($exception);
        }
    }

    public function failedAttempt($exception)
    {
        $this->site->number_of_attempts_update_the_page++;

        if ($this->site->number_of_attempts_update_the_page >= 3)
            $this->site->update_the_page = false;

        $this->site->save();

        $this->info(__('Error updating site content'));

        return false;
    }

    public function getResponse(Client $client, $url): \Psr\Http\Message\ResponseInterface
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36',
            'Referer' => (string)Url::fromString($url)->withPath('/')
        ];

        $response = $client->request('GET', (string)$url, [
            'allow_redirects' => [
                'max' => 5,             // allow at most 10 redirects.
                'strict' => false,      // use "strict" RFC compliant redirects.
                'referer' => true,      // add a Referer header
            ],
            'connect_timeout' => 15,
            'read_timeout' => 15,
            'headers' => $headers,
            'timeout' => 15
        ]);

        return $response;
    }

    public function parseEncodingFromHeader(string $headerStr)
    {
        if (preg_match('/charset=([A-z0-9\-]*)/iu', $headerStr, $matches)) {
            $encoding = $matches[1];
        }

        if (!empty($encoding)) {
            $encoding = mb_strtolower($encoding);
            return $encoding;
        } else
            return false;
    }

    public function parseEncodingFromHtml($html)
    {
        $crawler = new Crawler($html);

        $result = $crawler
            ->filter('head > meta[charset]')
            ->first()
            ->extract(['charset']);

        if (!empty($result))
            $encoding = pos($result);

        if (empty($encoding)) {
            $results = $crawler
                ->filter('head > meta[http-equiv]');

            foreach ($results as $node) {
                if (mb_strtolower($node->getAttribute('http-equiv')) == 'content-type') {
                    if ($node->hasAttribute('content')) {
                        if (preg_match('/charset=([A-z0-9\-]*)/iu', $node->getAttribute('content'), $matches)) {
                            $encoding = $matches[1];
                        }
                    }
                }
            }
        }

        if (!empty($encoding)) {
            $encoding = trim($encoding);
            $encoding = mb_strtolower($encoding);
            return $encoding;
        } else {
            return false;
        }
    }

    public function convertToUtf8Encoding($content, $encoding): string
    {
        try {
            $content = mb_convert_encoding($content, 'utf-8', $encoding);
        } catch (\Exception $exception) {
            if ($exception->getCode() == 2) {
                $content = iconv($encoding, 'utf-8', $content);
            }
        }

        $content = preg_replace('/\<meta.*?charset.*?\>/iu', '', $content);

        return (string)$content;
    }
}
