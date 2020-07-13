<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserInvitation;
use Faker\Generator as Faker;

$factory->define(UserInvitation::class, function (Faker $faker) {
    return [
        'email' => $faker->freeEmail
    ];
});
