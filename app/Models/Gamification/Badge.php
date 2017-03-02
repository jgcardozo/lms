<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'g_badges';

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function event() {
		return $this->belongsTo('App\Models\Gamification\Event');
	}

	public function milestone() {
		return $this->belongsTo('App\Models\Gamification\Milestone', 'id', 'badge_id');
	}
}
