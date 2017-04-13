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

		// FIX: If the slug is same with the old value while
		// updating it rewrites with another slug
		static::updating(function($model)
		{
			if(!array_key_exists('slug', $model->attributes))
				return;

			if(empty(request()->get('slug')))
			{
				$model->attributes['slug'] = $model->original['slug'];
			}
		});
	}
}