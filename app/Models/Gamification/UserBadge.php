<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
	protected $table = 'g_badge_user';

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function badge() {
		return $this->belongsTo('App\Models\Gamification\Badge');
	}
}
