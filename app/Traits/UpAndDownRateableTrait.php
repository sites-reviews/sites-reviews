<?php

namespace App\Traits;

use App\User;

trait UpAndDownRateableTrait
{
    public function getRateableModel()
    {
        return defined('static::RATEABLE_MODEL') ? static::RATEABLE_MODEL : '/';
    }

    public function ratings()
    {
        return $this->hasMany($this->getRateableModel(), 'rateable_id');
    }

    public function authUserRatings()
    {
        return $this->hasMany($this->getRateableModel(), 'rateable_id')
            ->where('create_user_id', auth()->id());
    }

    public function getAuthUserVote() :int
    {
        if ($this->authUserRatings->first())
            return $this->authUserRatings->first()->rating;
        else
            return 0;
    }

    public function rate(User $user, $number)
    {
        $rating = $this->ratings()
            ->withTrashed()
            ->where('create_user_id', $user->id)
            ->first();

        if (empty($rating))
        {
            $class = $this->getRateableModel();
            $rating = new $class();
        }

        $rating->create_user()->associate($user);

        if ($rating->trashed()) {
            $rating->restore();
            $rating->rating = $number;
        }
        else
        {
            if ($number > 0)
            {
                if ($rating->rating > 0)
                    $rating->rating = 0;
                else
                    $rating->rating = $number;
            }
            elseif ($number < 0)
            {
                if ($rating->rating < 0)
                    $rating->rating = 0;
                else
                    $rating->rating = $number;
            }
        }

        if ($rating->exists)
            $rating->save();
        else
            $this->ratings()->save($rating);

        $rating->refresh();

        return $rating;
    }

    public function ratingsUpdate()
    {
        $this->rate_up = $this->ratings()->where('rating', '>', 0)->count();
        $this->rate_down = $this->ratings()->where('rating', '<', 0)->count();
        $this->rating = $this->rate_up - $this->rate_down;
    }
}
