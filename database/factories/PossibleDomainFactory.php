<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PossibleDomain;
use Faker\Generator as Faker;

$factory->define(PossibleDomain::class, function (Faker $faker) {
    return [
        'domain' => \Illuminate\Support\Str::random(8).$faker->domainName
    ];
});
