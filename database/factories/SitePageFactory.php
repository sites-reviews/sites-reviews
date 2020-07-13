<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SitePage;
use Faker\Generator as Faker;

$factory->define(SitePage::class, function (Faker $faker) {
    return [
        'site_id' => function () {
            return factory(\App\Site::class)
                ->create()
                ->id;
        },
        'content' => '<html><head><title>Title</title></head><body><p>text</p></body></html>'
    ];
});
