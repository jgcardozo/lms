<?php

namespace App\Gamification\Contracts;

use Illuminate\Support\Facades\Facade;


class Gamification extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'App\Gamification\Gamification';
	}
}