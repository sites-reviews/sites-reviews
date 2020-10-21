<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => str_replace('.', '', $faker->userName),
        'email' => mb_strtolower(Str::random(8)).$faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => $faker->password(12, 20), // password
        'remember_token' => Str::random(10),
        'gender' => \App\Enums\Gender::getRandomKey()
    ];
});

$factory->afterCreatingState(User::class, 'verified', function (User $user, $faker) {
    $user->email_verified_at = now();
});

$factory->afterCreatingState(User::class, 'not_verified', function (User $user, $faker) {
    $user->email_verified_at = null;
});

$factory->afterCreatingState(User::class, 'with_welcome_notification', function (User $user, $faker) {
    $user->notify(new WelcomeNotification($user));
});

$factory->afterCreatingState(User::class, 'with_avatar', function (User $user, $faker) {

    $imagick = new \Imagick();
    $imagick->newImage(rand(200, 600), rand(200, 600), new \ImagickPixel('white'));
    $imagick->addNoiseImage(\Imagick::NOISE_RANDOM, \Imagick::CHANNEL_DEFAULT);
    $imagick->setImageFormat('jpeg');

    $avatar = new Image();
    $avatar->open($imagick);
    $avatar->save();

    $previewImagick = clone $imagick;
    $previewImagick->cropThumbnailImage(300,300);

    $avatarPreview = new Image();
    $avatarPreview->open($previewImagick);
    $avatarPreview->save();

    $user->avatar()->associate($avatar);
    $user->avatarPreview()->associate($avatarPreview);
    $user->save();

});

