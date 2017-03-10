<?php

namespace App\Traits;


trait BackpackUpdateLFT
{
	public static function bootBackpackUpdateLFT()
	{
		static::creating(function ($model)
		{
			$item = \DB::table($model->getTable())->orderBy('lft', 'desc')->take(1)->first();
			$lft_count = empty($item) || $item->lft == 0 ? 1 : $item->lft + 1;
			$model->lft = $lft_count;
		});
	}
}