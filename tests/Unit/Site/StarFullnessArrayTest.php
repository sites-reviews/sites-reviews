<?php

namespace Tests\Unit\Site;

use App\Library\StarFullness;
use App\Site;
use App\View\Components\SiteRating;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StarFullnessArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test4()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(4);

        $array = $starFullness->getArray();

        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('filled', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.0', $starFullness->getFractionalRemainder());
    }

    public function test3_14()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(3.14);

        $array = $starFullness->getArray();

        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('empty', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.14', $starFullness->getFractionalRemainder());
    }

    public function test3_44()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(3.44);

        $array = $starFullness->getArray();


        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('half', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.44', $starFullness->getFractionalRemainder());
    }

    public function test3_67()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(3.67);

        $array = $starFullness->getArray();


        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('half', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.67', $starFullness->getFractionalRemainder());
    }

    public function test3_75()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(3.75);

        $array = $starFullness->getArray();


        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('half', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.75', $starFullness->getFractionalRemainder());
    }

    public function test3_74()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(3.74);

        $array = $starFullness->getArray();


        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('half', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.74', $starFullness->getFractionalRemainder());
    }

    public function test3_76()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(3.76);

        $array = $starFullness->getArray();

        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('filled', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.76', $starFullness->getFractionalRemainder());
        $this->assertEquals('rgb(100, 175, 0)', $starFullness->getColor());
    }

    public function test3_86()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(3.86);

        $array = $starFullness->getArray();


        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('filled', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.86', $starFullness->getFractionalRemainder());
    }

    public function test0_15()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(0.15);

        $array = $starFullness->getArray();


        $this->assertEquals('empty', $array[1]);
        $this->assertEquals('empty', $array[2]);
        $this->assertEquals('empty', $array[3]);
        $this->assertEquals('empty', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.15', $starFullness->getFractionalRemainder());
    }

    public function test0_74()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(0.74);

        $array = $starFullness->getArray();

        $this->assertEquals('half', $array[1]);
        $this->assertEquals('empty', $array[2]);
        $this->assertEquals('empty', $array[3]);
        $this->assertEquals('empty', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.74', $starFullness->getFractionalRemainder());
    }

    public function test4_44()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(4.44);

        $array = $starFullness->getArray();

        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('filled', $array[4]);
        $this->assertEquals('half', $array[5]);
        $this->assertEquals('0.44', $starFullness->getFractionalRemainder());
    }

    public function test4_76()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate(4.76);

        $array = $starFullness->getArray();

        $this->assertEquals('filled', $array[1]);
        $this->assertEquals('filled', $array[2]);
        $this->assertEquals('filled', $array[3]);
        $this->assertEquals('filled', $array[4]);
        $this->assertEquals('filled', $array[5]);
        $this->assertEquals('0.76', $starFullness->getFractionalRemainder());
    }

    public function testEmpty()
    {
        $starFullness = new StarFullness();

        $array = $starFullness->getArray();

        $this->assertEquals('star_color_0', $starFullness->getClassname());
        $this->assertEquals('rgb(69, 69, 69)', $starFullness->getColor());
        $this->assertEquals('empty', $array[1]);
        $this->assertEquals('empty', $array[2]);
        $this->assertEquals('empty', $array[3]);
        $this->assertEquals('empty', $array[4]);
        $this->assertEquals('empty', $array[5]);
        $this->assertEquals('0.0', $starFullness->getFractionalRemainder());
    }

    public function testAll()
    {
        $starFullness = new StarFullness();

        foreach (range(1, 100) as $number)
        {
            $starFullness->setRate(($number / 10));
            $array = $starFullness->getArray();
        }

        $this->assertTrue(true);
    }
}
