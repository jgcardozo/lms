<?php

namespace App\Models;

use App\Traits\ISLock;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\InfusionsoftController;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use ISLock;
	use HasRoles;
	use CrudTrait;
	use Notifiable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'contact_id', 'email', 'password', 'activation_code', 'timezone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPasswordNotification($token));
	}

	public function hasTag($tag)
	{
		if(empty($tag))
		{
			return false;
		}

		return (bool) $this->is_tags->contains('id', $tag);
	}

	public function getTZAttribute()
	{
		if(!$this->timezone)
		{
			return config('app.timezone');
		}

		return $this->timezone;
	}

	/**
	 * Syncs new IS tags
	 *
	 * @return (array) New tags attached
	 */
	public function syncIsTags()
	{
		// Sync Infusionsoft user tags
		$is = new InfusionsoftController($this);
		$newTags = $is->sync();

		if(!empty($newTags))
		{
			Log::info('User tags updated. | ID: ' . implode(', ', $newTags));
		}

		// Check for unlocked course/module/lesson/session
		// and notify the user
		/*
		$items = $is->checkUnlockedCourses($newTags);
		if(!empty($is))
		{
			foreach($items as $item)
			{
				$event->user->notify(new UnlockedByTag($item));
			}
		}
		*/

		return $is->sync();
	}


	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function profile()
    {
		return $this->hasOne('App\Models\Profile');
	}

	public function sessionsWatched()
    {
		return $this->belongsToMany('App\Models\Session', 'session_user');
	}

	public function is_tags()
    {
        return $this->belongsToMany('App\Models\ISTag', 'tag_user', 'user_id', 'tag_id')->withPivot('created_at');
    }

	public function fb_posted()
	{
		return $this->belongsToMany('App\Models\Lesson', 'fb_lesson', 'user_id', 'lesson_id');
	}
}
