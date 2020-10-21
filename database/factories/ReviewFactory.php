<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Review;
use Faker\Generator as Faker;

$factory->define(Review::class, function (Faker $faker) {
    return [
        'site_id' => function () {
            return factory(App\Site::class)
                ->create()
                ->id;
        },
        'advantages' => $faker->realText(100),
        'disadvantages' => $faker->realText(100),
        'comment' => $faker->realText(100),
        'create_user_id' => function () {
            return factory(App\User::class)
                ->create()
                ->id;
        },
        'rate' => rand(1, 5)
    ];
});

$factory->afterMakingState(Review::class, 'private', function (Review $review, $faker) {
    $review->statusPrivate();
});

$factory->afterMakingState(Review::class, 'accepted', function (Review $review, $faker) {
    $review->statusAccepted();
});
