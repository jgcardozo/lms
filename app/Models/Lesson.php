<?php

namespace App\Models;

use Auth;
use App\Traits\ISLock;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\LockViaUserDate;
use App\Traits\BackpackCrudTrait;
use App\Traits\BackpackUpdateLFT;
use App\Traits\UsearableTimezone;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Activitylog\Traits\LogsActivity;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Lesson extends Model
{
	use ISLock;
	use CrudTrait;
	use Sluggable;
	use LogsActivity;
	use LockViaUserDate;
	use BackpackCrudTrait;
	use UsearableTimezone;
	use BackpackUpdateLFT;
	use SluggableScopeHelpers;

	protected $fillable = [
		'title', 'slug', 'description', 'video_url', 'bonus_video_url', 'bonus_video_duration', 'bonus_video_text', 'fb_link', 'module_id', 'featured_image', 'lock_date'
	];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(new OrderScope);
	}

	/**
	 * Backpack title fix for session selector. Lessons with same name.
	 *
	 * @return string
	 */
	public function getBackpackCrudTitleAttribute()
	{
		return '[' . $this->module->title . '] - ' . $this->title;
	}

	/**
	 * Get progress array,
	 * all sessions vs completed sessions
	 *
	 * @param null $user
	 * @return array
	 */
	public function getProgress($user = null)
	{
		if(!$user) {
			$user = Auth::user();
		}

		if(is_numeric($user))
		{
			$user = \App\Models\User::find($user);
		}

		$sessions = $this->sessions;
		$watched = $user->sessionsWatched->where('lesson_id', $this->id);

		return [
			'sessions' => $sessions->pluck('id')->toArray(),
			'watched' => $watched->pluck('id')->toArray()
		];
	}

	/**
	 * Get progress in percentages
	 *
	 * @return string
	 */
	public function getProgressPercentage()
	{
		$progress = $this->getProgress();
		$sCount = count($progress['sessions']);
		$wCount = count($progress['watched']);

		if($sCount == 0 || $wCount == 0)
		{
			$percentage = 0;
		}else{
			$percentage = ($wCount / $sCount) * 100;
		}

		return number_format($percentage, 2);
	}

	/**
	 * Get previous lesson within its module
	 *
	 * @return bool
	 */
	public function getPreviousLessonAttribute()
	{
		$prevLesson = $this->module->lessons->where('lft', '<', $this->lft)->last();

		return !$prevLesson ? false : $prevLesson;
	}

	/**
	 * Check if all the sessions within this lesson
	 * have been marked as watched by the user
	 *
	 * @return bool
	 */
	public function getIsCompletedAttribute($user = null)
	{
		$progress = $this->getProgress($user);

		return count($progress['sessions']) == count($progress['watched']) && count($progress['sessions']) > 0;
	}

	/**
	 * Check if lesson is locked
	 *
	 * @return bool
	 */
	public function getIsLockedAttribute()
	{
		if(is_role_admin())
			return false;

		if(!$this->course->is_locked && is_role_vip())
			return false;

		if($this->module->is_locked) {
			return true;
		}

		if($this->is_tag_locked())
		{
			return true;
		}

		// Get previous lesson
		$prevLesson = $this->previous_lesson;
		if((!$prevLesson || $prevLesson->is_completed) && !$this->is_date_locked)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get lesson duration. Sum of all sessions
	 *
	 * @return mixed
	 */
	public function getDurationAttribute()
	{
		return $this->sessions->sum('video_duration');
	}

	/**
	 * Get sessions count in this lesson
	 *
	 * @return int
	 */
	public function getSessionsCountAttribute()
	{
		return count($this->sessions);
	}

	/**
	 * Get image from S3
	 */
	public function getFeaturedImageUrlAttribute()
	{
		// TODO: Check why this is not working
		// $s3image = \Storage::disk('s3')->url($this->featured_image);

		return !empty($this->featured_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . rawurlencode($this->featured_image) : '';
	}

	/**
	 * Check If this lesson has bonus video
	 *
	 * @return bool
	 */
	public function getHasBonusAttribute()
	{
		return !empty($this->bonus_video_url);
	}

	public function getIsFbPostedAttribute()
	{
		$user = Auth::user();

		return $this->usersPosted()->where('user_id', $user->id)->exists();
	}

	public function getIsQAnsweredAttribute()
	{
		$user = Auth::user();

		return $this->userAnswered()->where('user_id', $user->id)->exists();
	}

	/**
	 * Get user lock date via course model
	 *
	 * @return Carbon\Carbon
	 */
	public function getUserLockDateAttribute()
	{
		return $this->course->user_lock_date;
	}

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function course()
	{
		return $this->module()->getResults()->belongsTo('App\Models\Course');
	}

	public function module()
	{
		return $this->belongsTo('App\Models\Module');
	}

	public function sessions()
	{
		return $this->hasMany('App\Models\Session');
	}

	public function usersPosted()
	{
		return $this->belongsToMany('App\Models\User', 'fb_lesson');
	}

	public function questions()
	{
		return $this->hasMany('App\Models\LessonQuestion');
	}
	
	public function userAnswered()
	{
		return $this->belongsToMany('App\Models\User', 'question_user');
	}

	public function sluggable()
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

	/*
	|--------------------------------------------------------------------------
	| Mutators
	|--------------------------------------------------------------------------
	*/
	public function setFeaturedImageAttribute($value)
	{
		$attribute_name = 'featured_image';
		$disk = 's3';
		$destination_path = 'lessons/';

		$request = \Request::instance();
		$file = $request->file($attribute_name);
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();

		// Make the image
		$image = \Image::make($file);

		// Store the image on disk
		\Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString());

		// Save the path to the database
		$this->attributes[$attribute_name] = $destination_path . $filename;
	}

	/*
	|--------------------------------------------------------------------------
	| Backpack model callbacks
	|--------------------------------------------------------------------------
	*/
	public function view_sessions_button()
	{
		?>
		<a href="<?php echo route('crud.session.index', ['lesson' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View sessions
		</a>
		<?php
	}

	public function admin_module_link()
	{
		if(!$this->module) return;

		?>
		<a href="<?php echo route('crud.module.edit', [$this->module->id]); ?>">
			<?php echo $this->module->title ?>
		</a>
		<?php
	}

	public function view_in_frontend_button()
	{
		?>
		<a target="_blank" href="<?php echo route('single.lesson', $this->slug); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View lesson
		</a>
		<?php
	}
}