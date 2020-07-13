<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Comment extends Component
{
    private $comment;
    private $descendants;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(\App\Comment $comment, $descendants = [])
    {
        $this->comment = $comment;
        $this->descendants = $descendants;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.comment', [
            'comment' => $this->comment,
            'descendants' => $this->descendants
        ]);
    }
}
