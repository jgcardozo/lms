<?php

namespace App\Models;

use Auth;
use App\Traits\ISLock;
use App\Scopes\OrderScope;
use App\Traits\IsFreeWatch;
use Backpack\CRUD\CrudTrait;
use App\Traits\LockViaUserDate;
use App\Traits\BackpackCrudTrait;
use App\Traits\BackpackUpdateLFT;
use App\Traits\UsearableTimezone;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\IgnoreCoachingCallsScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Activitylog\Traits\LogsActivity;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Session extends Model
{
	use ISLock;
	use CrudTrait;
	use Sluggable;
	use IsFreeWatch;
	use LogsActivity;
	use LockViaUserDate;
	use UsearableTimezone;
	use BackpackCrudTrait;
	use BackpackUpdateLFT;
	use SluggableScopeHelpers;

	protected $fillable = [
		'title', 'slug', 'description', 'video_url', 'video_duration', 'bucket_url', 'type', 'course_id', 'featured_image', 'starter_course_id', 'lesson_id', 'lock_date', 'learn_more'
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
		static::addGlobalScope(new IgnoreCoachingCallsScope);
	}

	/**
	 * Mark this session as watched by user
	 *
	 * @param $user
	 */
	public function markAsComplete($user = null)
    {
		if(!$user) {
			$user = Auth::user();
		}

		$this->usersWatched()->sync([$user->id], false);
	}

	/**
	 * Check if this session is marked as seen
	 *
	 * @return bool
	 */
	public function getIsCompletedAttribute($user = null)
    {
		if(!$user) {
			$user = Auth::user();
		}

		return $this->usersWatched()->where('user_id', $user->id)->exists();
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

	public function getVideoProgressAttribute()
	{
		$user_id = Auth::user()->id;
		$key = 'session_' . $this->id . '_' . $user_id;

		return session($key, 0);
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
	public function resources()
    {
		return $this->belongsToMany('App\Models\Resource', 'resource_session');
	}

	public function lesson()
    {
		return $this->belongsTo('App\Models\Lesson');
	}

	public function course()
    {
		if(!empty($this->starter_course_id))
		{
			return $this->belongsTo('App\Models\Course', 'starter_course_id', 'id');
		}

		return $this->lesson()->getResults()->module()->getResults()->belongsTo('App\Models\Course');
	}

	public function starter_course()
	{
		return $this->belongsTo('App\Models\Course', 'starter_course_id', 'id');
	}

	public function usersWatched()
    {
		return $this->belongsToMany('App\Models\User', 'session_user');
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
		$destination_path = 'sessions/';

		$request = \Request::instance();
		$file = $request->file($attribute_name);
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();

		// Make the image
		$image = \Image::make($file);

		// Store the image on disk
		\Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString(), 'public');

		// Save the path to the database
		$this->attributes[$attribute_name] = $destination_path . $filename;
	}

	/*
	|--------------------------------------------------------------------------
	| Backpack model callbacks
	|--------------------------------------------------------------------------
	*/
	public function admin_lesson_link()
	{
		if(!$this->lesson) return;

		ob_start();
		?>
		<a href="<?php echo route('crud.lesson.edit', [$this->lesson->id]); ?>">
			<?php echo $this->lesson->title ?>
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}

	public function admin_course_link()
	{
		if(!$this->starter_course) return;

		ob_start();
		?>
		<a href="<?php echo route('crud.course.edit', [$this->starter_course->id]); ?>">
			<?php echo $this->starter_course->title ?>
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}

	public function view_in_frontend_button()
	{
		if(!$this->lesson)
			return;
		?>
		<a target="_blank" href="<?php echo route('single.lesson', [$this->lesson->slug, 'session' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View session
		</a>
		<?php
	}

	public function reorder_resources_button()
	{
		if(!$this->resources)
			return;

		?>
		<a href="<?php echo route('crud.resource.reorder', ['session' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-arrows" aria-hidden="true"></i>
			Reorder resources
		</a>
		<?php
	}
}
