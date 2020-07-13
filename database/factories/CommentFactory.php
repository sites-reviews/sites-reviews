<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'review_id' => function () {
            return factory(\App\Review::class)
                ->create();
        },
        'text' => $faker->realText(200),
        'create_user_id' => function () {
            return factory(\App\User::class)
                ->create();
        }
    ];
});

$factory->afterCreatingState(Comment::class, 'with_reply', function (Comment $comment, $faker) {

    $parent = factory(Comment::class)
        ->create(['parent' => $comment->id]);
});
