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
	private $log = null;

	/**
	 * @var int
	 */
	private $streakDays = 3;

	public function __construct()
	{
		$this->get_log();
	}

	private function loadFromDb()
	{
		$user = Auth::user();
		$type = self::class;

		return DB::table('g_streak_logs')->where('user_id', $user->id)->where('type', $type)->orderBy('updated_at')->first();
	}

	public function get_log()
	{
		if(is_null($this->log))
		{
			$this->log = $this->loadFromDb();
		}

		return $this->log;
	}

	public function is_active()
	{
		if(empty($this->get_log()))
		{
			return false;
		}

		$last = $this->get_log();

		$lastLogDate = Carbon::parse($last->updated_at);
		$firstDay = Carbon::now()->subDays($this->streakDays - 1)->startOfDay();
		$lastDay = Carbon::now()->endOfDay();

		return $lastLogDate->between($firstDay, $lastDay);
	}

	public function started()
	{
		return 'N/A';
	}

	public function last_date()
	{
		return 'N/A';
	}

	public function consecutive()
	{
		// TODO: Implement consecutive() method.
	}

	public function is_logged()
	{
		if(empty($this->get_log()))
		{
			return false;
		}

		$todayStart = Carbon::now()->startOfDay();
		$todayEnd = Carbon::now()->endOfDay();

		$lastLog = Carbon::parse($this->log->updated_at);

		return $lastLog->between($todayStart, $todayEnd);
	}

	public function log()
	{
		if(!$this->is_active())
		{
			$this->log->count = 0;
		}else{
			$this->log->count++;
		}

		$this->log->updated_at = Carbon::now();

		DB::table('g_streak_logs')->where('id', $this->log->id)->update((array)$this->log);
	}
}