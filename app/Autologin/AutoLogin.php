<?php

namespace App\Autologin;

use DB;

class AutoLogin
{
	/**
	 * Generate and save the new key token
	 *
	 * @param string $newKey
	 */
	private function generateNewKey($key = '')
	{
		if(empty($key))
		{
			$key = uniqid('', true);
		}

		if(empty($this->getKey()))
		{
			DB::insert("insert into settings (`key`, `value`) values ('auto_login_key', ?)", [$key]);
		}else{
			DB::update("update settings set `value` = ? where `key`='auto_login_key'", [$key]);
		}
	}

	/**
	 * Get key token
	 *
	 * @return string
	 */
	public function getKey()
	{
		return DB::table('settings')->select('value')->where('key', 'auto_login_key')->value('value');
	}

	/**
	 * Refresh the key token
	 *
	 * @param string $key
	 */
	public function refreshKey($key = '')
	{
		$this->generateNewKey($key);
	}

	/**
	 * Validate the user and key token
	 *
	 * @param $id   User ID
	 * @param $mail User E-mail
	 * @param $key  Autologin key token
	 *
	 * @return bool
	 */
	public function validate($id, $mail, $key)
	{
		$user = \App\Models\User::find($id);

		return $key === $this->getKey()
				&& !empty($user)
				&& $user->email === $mail
				&& !$user->hasRole(['Administrator','Editor']);
	}
}