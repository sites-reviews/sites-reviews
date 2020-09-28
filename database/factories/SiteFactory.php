<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Site;
use Faker\Generator as Faker;

$factory->define(Site::class, function (Faker $faker) {
    return [
        'domain' => \Illuminate\Support\Str::random(8).$faker->domainName,
        'title' => $faker->company,
        'description' => $faker->realText(300),
        'create_user_id' => function () {
            return factory(\App\User::class)
                ->create()
                ->id;
        },
        'rating' => rand(1, 5)
    ];
});

$factory->afterCreatingState(Site::class, 'with_preview', function (Site $site, $faker) {

    $image = factory(\App\Image::class)
        ->create(['storage' => 'public']);

    $site->preview_image_id = $image->id;
    $site->save();
});

$factory->afterMakingState(Site::class, 'with_cyrillic_domain', function (Site $site, $faker) {

    $array = ['a', 'б', 'в', 'г', 'д', 'е', 'ж'];

    $site->domain = $array[array_rand($array)].
        $array[array_rand($array)].
        $array[array_rand($array)].
        $array[array_rand($array)].'.рф';
});
