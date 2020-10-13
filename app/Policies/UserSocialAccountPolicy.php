<?php

namespace App\Policies;

use App\User;
use App\UserSocialAccount;

class UserSocialAccountPolicy extends Policy
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

	public function unbind(User $auth_user, UserSocialAccount $account)
	{
		return $account->user_id == $auth_user->id;
	}
}
