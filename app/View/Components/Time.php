<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Time extends Component
{
    public $time;

    /**
     * Create the component instance.
     *
     * @param  string  $time
     * @return void
     */
    public function __construct($time)
    {
        $this->time = $time;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return $this->time->diffForHumans();
    }
}
