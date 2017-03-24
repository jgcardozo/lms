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
		// Check if streak is logged by its rule (daily, 3 days ...)
		if(!$streak->is_logged())
		{
			$streak->log();
		}
	}
}