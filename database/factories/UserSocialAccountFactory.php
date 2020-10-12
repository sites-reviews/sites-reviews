<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserSocialAccount;
use Faker\Generator as Faker;

$factory->define(UserSocialAccount::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\User::class)->create();
        },
        'provider_user_id' => rand(1, 10000000000),
        'provider' => 'google',
        'access_token' => $faker->linuxPlatformToken,
        'parameters' => '{"id": "' . rand(1, 10000000000) . '"}'
    ];
});
