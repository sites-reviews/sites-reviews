<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SiteOwner;
use Faker\Generator as Faker;

$factory->define(SiteOwner::class, function (Faker $faker) {
    return [
        'site_id' => function () {
            return factory(\App\Site::class)
                ->create()
                ->id;
        },
        'create_user_id' => function () {
            return factory(\App\User::class)
                ->create()
                ->id;
        }
    ];
});

$factory->state(SiteOwner::class, 'not_confirmed', function ($faker) {
    return [
        'status' => \App\Enums\StatusEnum::OnReview,
    ];
});
