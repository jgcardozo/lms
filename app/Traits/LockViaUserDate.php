<?php

namespace App\Traits;


trait LockViaUserDate
{
	/**
	 * Get user lock date via course model
	 *
	 * @return Carbon\Carbon
	 */
	public function getUserLockDateAttribute()
	{
		return $this->course->user_lock_date;
	}

	/**
	 * Check if item is locked
	 * with future date
	 *
	 * @return bool
	 */
	public function getIsDateLockedAttribute()
	{
		if(!empty($this->lock_date))
		{
			$expire = strtotime($this->lock_date);
			$today = strtotime('today midnight');

			if($today >= $expire)
			{
				return false;
			}
		}

		if(!empty($this->lock_date) && !$this->user_lock_date)
		{
			return $today >= $expire ? false : true;
		}

		if(!empty($this->lock_date) && $this->user_lock_date)
		{
			// If user registration date is not greater then item user_lock_date,
			// that means this item was somewhen unlocked for those same users
			if( ! \Auth::user()->created_at->gt( $this->user_lock_date ) )
			{
				return false;
			}

			return $today >= $expire ? false : true;
		}

		return false;
	}
}