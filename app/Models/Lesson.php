<?php

namespace App\Models;

use Auth;
use App\Traits\ISLock;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
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
	use BackpackCrudTrait;
	use SluggableScopeHelpers;

	protected $fillable = ['title', 'description', 'video_url', 'module_id', 'featured_image', 'lock_date'];

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
		$percentage = ($wCount / $sCount) * 100;

		return number_format($percentage, 2);
	}

	/**
	 * Get previous lesson within its module
	 *
	 * @return bool
	 */
	public function getPreviousLessonAttribute()
	{
		$prevLesson = $this->module->lessons->where('lft', '<', $this->lft)->first();

		return !$prevLesson ? false : $prevLesson;
	}

	/**
	 * Check if all the sessions within this lesson
	 * have been marked as watched by the user
	 *
	 * @return bool
	 */
	public function getIsCompletedAttribute()
	{
		$progress = $this->getProgress();

		return count($progress['sessions']) == count($progress['watched']) && count($progress['sessions']) > 0;
	}

	/**
	 * Check if the module is locked
	 * with future date
	 *
	 * @return bool
	 */
	public function getIsDateLockedAttribute()
	{
		if(!empty($this->lock_date))
		{
			$expire = strtotime($this->lock_date);
			$today = strtotime('today midnight');

			return $today >= $expire ? false : true;
		}

		return false;
	}

	/**
	 * Check if lesson is locked
	 *
	 * @return bool
	 */
	public function getIsLockedAttribute()
	{
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
	 * Get lesson duration. Sum of all sessions.
	 *
	 * @return mixed
	 */
	public function getDurationAttribute()
	{
		return $this->sessions->sum('video_duration');
	}

	public function getSessionsCountAttribute()
	{
		return count($this->sessions);
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
		$destination_path = 'lesson_' . $this->slug . '/';

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
		ob_start();
		?>
		<a href="<?php echo route('crud.session.index', ['lesson' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View sessions
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}

	public function admin_module_link()
	{
		if(!$this->module) return;

		ob_start();
		?>
		<a href="<?php echo route('crud.module.edit', [$this->module->id]); ?>">
			<?php echo $this->module->title ?>
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}
}