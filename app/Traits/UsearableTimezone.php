<?php

namespace App\Traits;


trait UsearableTimezone
{
	/**
	 * Model attribute
	 *
	 * @param $attribute
	 */
	public function getDate($attribute)
	{
		if(isset($this->{$attribute}))
		{
			if(!($this->{$attribute} instanceof \Carbon\Carbon))
			{
				return $this->asDateTime($this->{$attribute})->timezone(\Auth::user()->tz);
			}

			return $this->{$attribute}->timezone(\Auth::user()->timezone);
		}

		return false;
	}
}