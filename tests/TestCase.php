<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;
    use DatabaseTransactions;

    private $images;

    public function fakeImageStream($width = 300, $height = 300, $extension = 'jpeg')
    {
        $tmp = tmpfile();

        $imagick = new \Imagick();
        $imagick->newImage($width, $height, new \ImagickPixel('white'));
        $imagick->addNoiseImage(\Imagick::NOISE_RANDOM, \Imagick::CHANNEL_DEFAULT);
        $imagick->setImageFormat($extension);

        fwrite($tmp, $imagick->getImageBlob());

        $key = uniqid();

        $this->images[$key] = $tmp;

        return stream_get_meta_data($this->images[$key])['uri'];
    }
}
