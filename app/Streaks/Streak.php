<?php

namespace App\Streaks;

use DB;
use Auth;
use App\Streaks\Contracts\StreakInterface;

class Streak
{
	/**
	 * Log the streak
	 *
	 * @param \App\Streaks\Contracts\StreakInterface $streak
	 */
	public static function log(StreakInterface $streak)
	{
		$user = Auth::user();
		$type = get_class($streak);

		if(!$streak->is_logged())
		{
			DB::insert('INSERT INTO g_streak_logs (user_id, type) VALUES (?, ?)', [$user->id, $type]);
		}
	}
}