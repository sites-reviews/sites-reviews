<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ReviewRating;
use Faker\Generator as Faker;

$factory->define(ReviewRating::class, function (Faker $faker) {
    return [
        'rateable_id' => function () {
            return factory(\App\Review::class)
                ->create();
        },
        'rating' => '1',
        'create_user_id' => function () {
            return factory(\App\User::class)
                ->create();
        },
    ];
});

$factory->state(ReviewRating::class, 'up', function ($faker) {
    return [
        'rating' => 1
    ];
});

$factory->state(ReviewRating::class, 'down', function ($faker) {
    return [
        'rating' => -1
    ];
});

