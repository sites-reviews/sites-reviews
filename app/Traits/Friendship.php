<?php

namespace App\Traits;

use App\Enums\UserRelationType;

trait Friendship
{
	public $relation_to_users = [];

	public function isNobodyTo($user)
	{
		$relation = $this->relationships
			->where('user_id', $user->id)
			->first();

		if (is_null($relation) or $relation->type == UserRelationType::Null) {
			if (!$this->isSubscriptionOf($user) and !$this->addedToBlacklistBy($user))
				return true;
		}

		return false;
	}

	public function isSubscriptionOf($user)
	{
		$relation = $this->relationshipsReverse
			->where('create_user_id', $user->id)
			->first();

		if (optional($relation)->type == UserRelationType::Subscriber)
			return true;

		return false;
	}

	public function addedToBlacklistBy($user)
	{
		$relation = $this->relationshipsReverse
			->where('create_user_id', $user->id)
			->first();

		if (optional($relation)->type == UserRelationType::Blacklist)
			return true;

		return false;
	}

	public function isFriendOf($user)
	{
		$relation = $this->relationships
			->where('user_id', $user->id)
			->first();

		if (optional($relation)->type == UserRelationType::Friend)
			return true;

		return false;
	}

	public function isSubscriberOf($user)
	{
		$relation = $this->relationships
			->where('user_id', $user->id)
			->first();

		if (optional($relation)->type == UserRelationType::Subscriber)
			return true;

		return false;
	}

	public function hasAddedToBlacklist($user)
	{
		$relation = $this->relationships
			->where('user_id', $user->id)
			->first();

		if (optional($relation)->type == UserRelationType::Blacklist)
			return true;

		return false;
	}

	public function relationships()
	{
		return $this->hasMany('App\UserRelation', 'create_user_id', 'id');
	}

	public function friends()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'create_user_id', 'user_id')
			->wherePivot('type', UserRelationType::Friend);
	}

	// друзья

	public function subscriptions()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'create_user_id', 'user_id')
			->wherePivot('type', UserRelationType::Subscriber);
	}

	// подписки

	public function subscribers()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'user_id', 'create_user_id')
			->wherePivot('type', UserRelationType::Subscriber);
	}

	// подписчики

	public function blacklists()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'create_user_id', 'user_id')
			->wherePivot('type', UserRelationType::Blacklist);
	}

	// Черный список

	public function friendsAndSubscriptions()
	{
		return $this->relationshipsReverse()
			->whereIn('user_relations.status', [UserRelationType::Subscriber, UserRelationType::Friend]);
	}

	public function relationshipsReverse()
	{
		return $this->hasMany('App\UserRelation', 'user_id', 'id');
	}

	public function relationOnUser($user)
	{
		if (empty($this->relation_to_users[$user->id])) {

			$this->relation_to_users[$user->id] = $this->relationships
				->where('user_id', $user->id)
				->first();
		}
		return $this->relation_to_users[$user->id];
	}

	public function relation_to_user($user)
	{
		if (empty($this->relation_to_users[$user->id])) {

			$this->relation_to_users[$user->id] = $this->relationships
				->where('user_id', $user->id)
				->first();
		}
		return $this->relation_to_users[$user->id];
	}

	public function updateSubscribersCount()
    {
        $this->number_of_subscribers = $this->subscribers()->count();
    }

    public function updateSubscriptionsCount()
    {
        $this->number_of_subscriptions = $this->subscriptions()->count();
    }

}
