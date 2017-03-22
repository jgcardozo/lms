<?php

namespace app\InfusionsoftFlow\Facades;

use Illuminate\Support\Facades\Facade;

class InfusionsoftFlow extends Facade
{
	protected static function getFacadeAccessor() { return 'isflow'; }
}