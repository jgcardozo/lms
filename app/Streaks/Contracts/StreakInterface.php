<?php

namespace App\Streaks\Contracts;


interface StreakInterface
{
	/**
	 * Determinate if a user streak is active or not
	 *
	 * @return bool
	 */
	public function is_active();

	/**
	 * Get the user streak start date
	 *
	 * @return string Date when the streak is started
	 */
	public function started();

	/**
	 * Get the user streak last date
	 *
	 * @return string Date for the last consecutive
	 */
	public function last_date();

	/**
	 * Get the number of consecutive days/values tracked. Ex: 12 straight days that they logged in
	 * @return mixed
	 */
	public function consecutive();

	/**
	 * Determinate if the user streak was already logged dependent on its the rule
	 * @return bool
	 */
	public function is_logged();
}