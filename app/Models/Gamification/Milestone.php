<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
	protected $table = 'g_milestones';

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function badges() {
		return $this->belongsToMany('App\Models\Gamification\Badge', 'g_milestone_badges');
	}
}
