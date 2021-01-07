<?php

return [
    'request' => [
        'options' => [
            'connect_timeout' => 5,
            'read_timeout' => 5,
            'timeout' => 5,
            'allow_redirects' => [
                'max' => 5,
                'protocols' => ['http', 'https']
            ],
            'headers' => [
                'Accept' => [
                    'text/html',
                    'application/xhtml+xml',
                    'application/xml'
                ],
                'Accept-Encoding' => [
                    'gzip',
                    'deflate',
                    'br'
                ],
                'Accept-Language' => [
                    'en-EN',
                    'ru-RU',
                    'en-US',
                    'en'
                ],
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36'
            ]
        ]
    ]
];
