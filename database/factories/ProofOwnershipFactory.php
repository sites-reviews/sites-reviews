<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ProofOwnership;
use Faker\Generator as Faker;

$factory->define(ProofOwnership::class, function (Faker $faker) {
    return [
        'site_owner_id' => function () {
            return factory(\App\SiteOwner::class)
                ->create()
                ->id;
        }
    ];
});
