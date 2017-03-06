<?php

namespace App\Streaks\Types;

use DB;
use Auth;
use Carbon\Carbon;
use App\Streaks\Contracts\StreakInterface;

class LoginStreak implements StreakInterface
{
	/**
	 * @var string Streak type
	 */
	public $type = 'login';

	/**
	 * @var array Logs from DB
	 */
	private $logs = null;

	/**
	 * @var int
	 */
	private $streakDays = 3;

	public function __construct()
	{
		$this->loadFromDb();
	}

	private function loadFromDb()
	{
		$user = Auth::user();
		$type = self::class;

		$this->logs = DB::table('g_streak_logs')->where('user_id', $user->id)->where('type', $type)->orderBy('created_at')->get();
	}

	public function is_active()
	{
		if(is_null($this->logs))
		{
			$this->loadFromDb();
		}

		if(empty($this->logs))
		{
			return false;
		}

		$last = $this->logs->last();

		$lastLogDate = Carbon::parse($last->created_at);
		$firstDay = Carbon::now()->subDays($this->streakDays - 1)->startOfDay();
		$lastDay = Carbon::now()->endOfDay();

		return $lastLogDate->between($firstDay, $lastDay);
	}

	public function started()
	{
		if(!$this->is_active())
		{
			return false;
		}

		// First day is the day when the streak started. Today - streakDays
		$firstDay = Carbon::now()->subDays($this->streakDays - 1)->startOfDay();
		$_logs = $this->logs->where('created_at', '>=', $firstDay);

		return $_logs->first()->created_at;
	}

	public function last_date()
	{
		if(!$this->is_active())
		{
			return false;
		}

		// First day is the day when the streak started. Today - streakDays
		$firstDay = Carbon::now()->subDays($this->streakDays - 1)->startOfDay();
		$_logs = $this->logs->where('created_at', '>=', $firstDay);

		return $_logs->last()->created_at;
	}

	public function consecutive()
	{
		// TODO: Implement consecutive() method.
	}

	public function is_logged()
	{
		return false;
	}
}