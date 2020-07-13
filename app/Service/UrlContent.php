<?php

namespace App\Service;

use GuzzleHttp\Client;
use Litlife\Url\Url;

class UrlContent
{
    public function getContent($url, $handlerStack = null)
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36',
            'Referer' => (string)Url::fromString($url)->withPath('/')
        ];

        if (!empty($handlerStack))
            $client = new Client(['handler' => $handlerStack]);
        else
            $client = new Client();

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

        return $response->getBody()
            ->getContents();
    }
}
