<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Review extends Component
{
    private $review;
    private $showReviewedItem;
    private $showUserReviewsCount;
    private $comments;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($review, $showReviewedItem = false, $showUserReviewsCount = true, $comments = [])
    {
        $this->review = $review;
        $this->showReviewedItem = $showReviewedItem;
        $this->showUserReviewsCount = $showUserReviewsCount;
        $this->comments = $comments;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.review', [
            'review' => $this->review,
            'showReviewedItem' => $this->showReviewedItem,
            'showUserReviewsCount' => $this->showUserReviewsCount,
            'comments' => $this->comments
        ]);
    }
}
