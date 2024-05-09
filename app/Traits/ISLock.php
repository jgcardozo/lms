<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Infusionsoft tag lock content
 */
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
	{//dd("paso2- isLock/Trait");
		$user = Auth::user();
        //dd($this); //course
		$lock_tags = $this->lock_tags->pluck('id')->toArray();
		$user_tabs = $user->is_tags->pluck('id')->toArray();
        //dd($lock_tags, $user_tabs);
		$check = array_intersect($lock_tags, $user_tabs);
		//dd($check);

		if(count($check) > 0 || count($lock_tags) == 0)
		{//dd('devuelve false');
			return false;
		}
		//dd('devuelve verdad');
		return true;
	}
}