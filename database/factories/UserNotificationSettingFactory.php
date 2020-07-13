<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserNotificationSetting;
use Faker\Generator as Faker;

$factory->define(UserNotificationSetting::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\User::class)
                ->create();
        },
        'email_response_to_my_review' => true,
        'db_response_to_my_review' => true,
        'db_when_review_was_liked' => true,
    ];
});
