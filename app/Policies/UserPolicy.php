<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\Response;

class UserPolicy extends Policy
{
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function edit(User $authUser, User $user)
    {
        if ($user->is($authUser))
            return $this->allow();
        else
            return $this->deny(__('You do not have the right to edit this user profile'));
    }

    public function edit_notification_settings(User $authUser, User $user)
    {
        if ($user->is($authUser))
            return $this->allow();
        else
            return $this->deny(__('You do not have the right to change this user notification settings'));
    }

    public function see_notifications(User $authUser, User $user)
    {
        if ($user->is($authUser))
            return $this->allow();
        else
            return $this->deny(__('You do not have permission to view notifications'));
    }
}
