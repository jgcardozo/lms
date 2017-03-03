<?php
/**
 * Infusionsoft lock content tags
 */

namespace App\Traits;

use Auth;

trait ISLock
{
	/*
	|--------------------------------------------------------------------------
	| Model relations
	|--------------------------------------------------------------------------
	*/
	public function lock_tags()
	{
		return $this->morphToMany('App\Models\ISTag', 'lockable', 'is_lockables', 'lockable_id', 'tag_id');
	}

	/*
	|--------------------------------------------------------------------------
	| Model methods
	|--------------------------------------------------------------------------
	*/
	public function is_tag_locked()
	{
		$user = Auth::user();

		$lock_tags = $this->lock_tags->pluck('id')->toArray();
		$user_tabs = $user->is_tags->pluck('id')->toArray();

		$check = array_intersect($lock_tags, $user_tabs);

		if(count($check) == count($lock_tags))
		{
			return false;
		}

		return true;
	}
}