<?php

namespace App\Library;

class StarFullness
{
    private $rate;

    private $colorArray = [
        '0' => [
            'class' => 'star_color_0',
            'color' => 'rgb(69, 69, 69)'
        ],
        '1' => [
            'class' => 'star_color_10',
            'color' => 'rgb(255, 0, 0)'
        ],
        '1.5' => [
            'class' => 'star_color_15',
            'color' => 'rgb(255, 80, 0)'
        ],
        '2' => [
            'class' => 'star_color_20',
            'color' => 'rgb(255, 150, 0)'
        ],
        '2.5' => [
            'class' => 'star_color_25',
            'color' => 'rgb(200, 192, 0)'
        ],
        '3' => [
            'class' => 'star_color_30',
            'color' => 'rgb(200, 225, 0)'
        ],
        '3.5' => [
            'class' => 'star_color_35',
            'color' => 'rgb(120, 200, 0)'
        ],
        '4' => [
            'class' => 'star_color_40',
            'color' => 'rgb(100, 175, 0)'
        ],
        '4.5' => [
            'class' => 'star_color_45',
            'color' => 'rgb(50, 150, 0)'
        ],
        '5' => [
            'class' => 'star_color_50',
            'color' => 'rgb(0, 125, 0)'
        ],
    ];

    public function setRate($rate)
    {
        $this->rate = floatval($rate);
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getArray(): array
    {
        $stars = array_flip(range(1, 5));

        $fractionalRemainder = $this->getFractionalRemainder();

        foreach ($stars as $rate => $star) {
            if ($this->rate >= $rate) {
                $stars[$rate] = 'filled';
            } elseif ($this->rate > $rate - 1) {
                if ($fractionalRemainder > 0.75)
                    $stars[$rate] = 'filled';
                elseif ($fractionalRemainder > 0.25)
                    $stars[$rate] = 'half';
                else
                    $stars[$rate] = 'empty';
            } elseif ($this->rate < $rate) {
                $stars[$rate] = 'empty';
            }
        }

        return $stars;
    }

    public function getColor()
    {
        if ($this->rate < 1) {
            $color = $this->colorArray['0']['color'];
        } elseif ($this->rate <= 1.25) {
            $color = $this->colorArray['1']['color'];
        } elseif ($this->rate <= 1.75) {
            $color = $this->colorArray['1.5']['color'];
        } elseif ($this->rate <= 2.25) {
            $color = $this->colorArray['2']['color'];
        } elseif ($this->rate <= 2.75) {
            $color = $this->colorArray['2.5']['color'];
        } elseif ($this->rate <= 3.25) {
            $color = $this->colorArray['3']['color'];
        } elseif ($this->rate <= 3.75) {
            $color = $this->colorArray['3.5']['color'];
        } elseif ($this->rate <= 4.25) {
            $color = $this->colorArray['4']['color'];
        } elseif ($this->rate < 4.75) {
            $color = $this->colorArray['4.5']['color'];
        } else {
            $color = $this->colorArray['5']['color'];
        }

        return $color;
    }

    public function getClassname()
    {
        if ($this->rate < 1) {
            $color = $this->colorArray['0']['class'];
        } elseif ($this->rate <= 1.25) {
            $color = $this->colorArray['1']['class'];
        } elseif ($this->rate <= 1.75) {
            $color = $this->colorArray['1.5']['class'];
        } elseif ($this->rate <= 2.25) {
            $color = $this->colorArray['2']['class'];
        } elseif ($this->rate <= 2.75) {
            $color = $this->colorArray['2.5']['class'];
        } elseif ($this->rate <= 3.25) {
            $color = $this->colorArray['3']['class'];
        } elseif ($this->rate <= 3.75) {
            $color = $this->colorArray['3.5']['class'];
        } elseif ($this->rate <= 4.25) {
            $color = $this->colorArray['4']['class'];
        } elseif ($this->rate < 4.75) {
            $color = $this->colorArray['4.5']['class'];
        } else {
            $color = $this->colorArray['5']['class'];
        }

        return $color;
    }

    public function getFractionalRemainder(): float
    {
        return $this->rate - floor($this->rate);
    }
}
