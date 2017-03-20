<?php

namespace App\Models;

use App\Traits\ISLock;
use Backpack\CRUD\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;
	use CrudTrait;
	use HasRoles;
	use ISLock;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
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
        return $this->belongsToMany('App\Models\ISTag', 'tag_user', 'user_id', 'tag_id');
    }

	public function fb_posted()
	{
		return $this->belongsToMany('App\Models\Lesson', 'fb_lesson', 'user_id', 'lesson_id');
	}
}
