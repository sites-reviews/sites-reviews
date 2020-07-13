<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'create_user_id' => function () {
            return factory(App\User::class)
                ->create()
                ->id;
        },
    ];
});

$factory->afterMaking(Image::class, function ($image, $faker) {

    $width = rand(300, 1000);
    $height = rand(300, 1000);

    $colors = collect(['red', 'blue', 'yellow', 'orange', 'green', 'cyan']);

    $imagick = new Imagick();
    $imagick->newImage($width, $height, new ImagickPixel($colors->random()));

    $draw = new ImagickDraw();
    $pixel = new ImagickPixel( 'gray' );

    /* Черный текст */
    $draw->setFillColor('white');

    /* Настройки шрифта */
    $draw->setFontSize( 20 );

    /* Создаем текст */
    $imagick->annotateImage($draw, 10, 45, 0, $width.'x'.$height);

    $imagick->setImageFormat('jpeg');

    $image->open($imagick);
});
