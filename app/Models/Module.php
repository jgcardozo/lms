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

class Module extends Model
{
	use ISLock;
	use CrudTrait;
	use Sluggable;
	use LogsActivity;
	use SluggableScopeHelpers;
	use BackpackCrudTrait;

	protected $fillable = ['title', 'slug', 'description', 'video_url', 'course_id', 'lock_date', 'featured_image'];

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
	 * all lesson vs completed lessons
	 *
	 * @param null $user
	 * @return array
	 */
	public function getProgress($user = null)
	{
		$progress = [
			'sessions' => [],
			'watched' => []
		];

		foreach($this->lessons as $lesson)
		{
			$_progress = $lesson->getProgress($user);
			$progress['sessions'] = array_merge($progress['sessions'], $_progress['sessions']);
			$progress['watched'] = array_merge($progress['watched'], $_progress['watched']);
		}

		return $progress;
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
	 * Get previous module within its course
	 *
	 * @return bool
	 */
	public function getPreviousModuleAttribute()
	{
		$prevModule = $this->course->modules->where('lft', '<', $this->lft)->last();

		return !$prevModule ? false : $prevModule;
	}

	/**
	 * Check if all the lessons within this module
	 * have been marked as watched by the user
	 *
	 * @return bool
	 */
	public function getIsCompletedAttribute()
	{
		foreach($this->lessons as $lesson)
		{
			if(!$lesson->is_completed)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if the module is locked
	 * with future date
	 *
	 * @return bool
	 */
	public function getIsDateLockedAttribute()
	{
		if(!empty($this->lock_date)) {
			$expire = strtotime($this->lock_date);
			$today = strtotime('today midnight');

			return $today >= $expire ? false : true;
		}

		return false;
	}

	/**
	 * Check if module is locked by any reason
	 *
	 * @return bool Returns true if is locked
	 */
	public function getIsLockedAttribute()
	{
		if(is_role_admin())
			return false;

		if($this->course->is_locked || !$this->course->areAllStarterSeen())
		{
			return true;
		}

		if($this->is_tag_locked())
		{
			return true;
		}

		// Get previous module
		$prevModule = $this->previous_module;
		if((!$prevModule || $prevModule->is_completed) && !$this->is_date_locked)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get image from S3
	 */
	public function getFeaturedImageUrlAttribute()
	{
		// TODO: Check why this is not working
		// $s3image = \Storage::disk('s3')->url($this->featured_image);

		return !empty($this->featured_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . $this->featured_image : '';
	}

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function course()
	{
		return $this->belongsTo('App\Models\Course');
	}

	public function lessons()
	{
		return $this->hasMany('App\Models\Lesson');
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
		$destination_path = 'modules/';

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
	public function view_lessons_button()
	{
		ob_start();
		?>
		<a href="<?php echo route('crud.lesson.index', ['module' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View lessons
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}

	public function admin_course_link()
	{
		if(!$this->course) return;

		ob_start();
		?>
		<a href="<?php echo route('crud.course.edit', [$this->course->id]); ?>">
			<?php echo $this->course->title ?>
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}
}
