<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Notifications\WelcomeNotification;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\PasswordReset::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\User::class)
                ->create()
                ->id;
        },
        'email' => $faker->safeEmail,
        'token' => mb_strtolower(Str::random(60)),
        'created_at' => now(),
        'used_at' => null
    ];
});

