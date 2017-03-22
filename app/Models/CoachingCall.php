<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use App\Scopes\OnlyCoachingCallsScope;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class CoachingCall extends Model
{
	use CrudTrait, Sluggable, SluggableScopeHelpers, BackpackCrudTrait;

    protected $table = 'courses';

	protected $fillable = ['title', 'short_description', 'description', 'video_url', 'image', 'module_group_title', 'course_id', 'lock_date'];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(new OrderScope);
		static::addGlobalScope(new OnlyCoachingCallsScope);
	}

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function course()
	{
		return $this->belongsTo('App\Models\Course', 'course_id', 'id');
	}

	public function sluggable()
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

	public function getRouteKeyName()
	{
		return 'slug';
	}
}
