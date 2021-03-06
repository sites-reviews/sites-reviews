<?php

namespace App\Console\Commands\Site;

use App\Image;
use App\Service\UrlContent;
use App\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
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
    protected $signature = 'site:update_content {site}';

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
        $this->site = $this->getSite($this->argument('site'));

        try {
            $response = $this->getResponse($client, $this->site->getUrl());

            $content = $response->getBody()
                ->getContents();

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

            if (empty($encoding)) {
                $encoding = $this->parseEncodingFromHtml($content);
            }

            if ($encoding != 'utf-8') {
                if (!empty($encoding)) {
                    $content = $this->convertToUtf8Encoding($content, $encoding);
                }
            }

            $this->site->page->content = $content;
            $this->site->updateTitleIfEmpty();
            $this->site->updateDescriptionFromPage();
            $this->site->number_of_attempts_update_the_page = 0;
            $this->site->push();

            $this->info(__('Site content was updated successfully'));

            return true;

        } catch (ConnectException $exception) {
            return $this->failedAttempt($exception);
        } catch (RequestException $exception) {
            return $this->failedAttempt($exception);
        }
    }

    public function failedAttempt($exception)
    {
        Log::warning($exception);

        $this->site->number_of_attempts_update_the_page++;

        if ($this->site->number_of_attempts_update_the_page >= 3)
            $this->site->update_the_page = false;

        $this->site->save();

        $this->info(__('Error updating site content'));

        return false;
    }

    public function getResponse(Client $client, $url): \Psr\Http\Message\ResponseInterface
    {
        $options = config('guzzle.request.options');
        $options['connect_timeout'] = 15;
        $options['read_timeout'] = 15;
        $options['timeout'] = 15;
        $options['verify'] = false;
        $options['headers']['Referer'] = (string)Url::fromString($url)->withPath('/');

        $response = $client->request('GET', (string)$url, $options);

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
        $encoding = mb_strtolower($encoding);

        if ($encoding == 'cp1251')
        {
            $content = iconv($encoding, 'utf-8', $content);
        }
        else
        {
            try {
                $content = mb_convert_encoding($content, 'utf-8', $encoding);
            } catch (\Exception $exception) {
                if ($exception->getCode() == 2) {
                    $content = iconv($encoding, 'utf-8', $content);
                }
            }
        }

        $content = preg_replace('/\<meta.*?charset.*?\>/iu', '', $content);

        return (string)$content;
    }

    public function getSite($site)
    {
        if (intval($site))
            return Site::findOrFail($site);
        else {
            $url = Url::fromString($site);

            return Site::whereDomain($url->getHost())->firstOrFail();
        }
    }
}
