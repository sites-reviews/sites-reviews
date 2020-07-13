<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CommentRating;
use Faker\Generator as Faker;

$factory->define(CommentRating::class, function (Faker $faker) {
    return [
        'rateable_id' => function () {
            return factory(\App\Comment::class)
                ->create();
        },
        'rating' => '1',
        'create_user_id' => function () {
            return factory(\App\User::class)
                ->create();
        },
    ];
});

$factory->state(CommentRating::class, 'up', function ($faker) {
    return [
        'rating' => 1
    ];
});

$factory->state(CommentRating::class, 'down', function ($faker) {
    return [
        'rating' => -1
    ];
});
