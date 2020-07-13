<?php

namespace App\View\Components;

use App\Library\StarFullness;
use App\Site;
use Illuminate\View\Component;

class SiteRating extends Component
{
    public $rating;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(float $rating)
    {
        $this->rating = $rating;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.site-rating', $this->getViewArray());
    }

    public function getViewArray()
    {
        $starFullness = new StarFullness();
        $starFullness->setRate($this->rating);
        $color_class = $starFullness->getClassname();

        return [
            'rating' => $this->rating,
            'color_class' => $color_class,
            'stars' => $starFullness->getArray(),
            'fractionalRemainder' => $starFullness->getFractionalRemainder()
        ];
    }

    public function rbgToHex($red, $green, $blue)
    {
        return sprintf("#%02x%02x%02x", $red, $green, $blue);
    }

    public function test($rating)
    {
        if ($rating >= 2.5)
        {
            $color = [round((255 * (5 - $rating)) / 2.5 ), round((255 * (5 - $rating)) / 2.5 ), 0];
        }
        elseif ($rating < 2.5)
        {
            $color = [255, round((255 * $rating) / 2.5 ), 0];
        }

        return $color;
    }
}
