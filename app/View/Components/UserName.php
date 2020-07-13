<?php

namespace App\View\Components;

use App\User;
use Illuminate\View\Component;

class UserName extends Component
{
    /**
     * The alert type.
     *
     * @var string
     */
    public $user;
    public $url;

    /**
     * Create the component instance.
     *
     * @param  User  $user
     * @param  boolean  $url
     * @return void
     */
    public function __construct($user, $url = true)
    {
        $this->user = $user;

        if ($url === 'true')
            $url = true;

        if ($url === 'false')
            $url = false;

        $this->url = boolval($url);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if (empty($this->user))
        {
            return __('user.deleted');
        }

        if ($this->user->trashed())
        {
            return __('user.deleted');
        }

        if (!$this->url)
        {
            return $this->user->name;
        }
        else
        {
            return '<a href="'.route('users.show', $this->user).'" class="font-weight-bold">'. $this->user->name. '</a>';
        }
    }
}
