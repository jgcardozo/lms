<?php

namespace App\Autologin\Facades;

use Illuminate\Support\Facades\Facade;


class AutoLogin extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'autologin';
	}
}