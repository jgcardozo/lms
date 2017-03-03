<?php

namespace App\Models;

use Auth;
use App\Traits\ISLock;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Module extends Model
{
	use CrudTrait;
	use Sluggable;
	use SluggableScopeHelpers;
	use BackpackCrudTrait;
	use ISLock;

	protected $fillable = ['title', 'description', 'video_url', 'course_id', 'lock_date'];

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
		if(!$user) {
			$user = Auth::user();
		}

		$lessons = $this->lessons;

		$watched = [];
		foreach($lessons as $lesson) {
			if($lesson->is_completed) {
				$watched[] = $lesson->id;
			}
		}

		return [
			'lessons' => $lessons->pluck('id')->toArray(),
			'watched' => $watched
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
		$sCount = count($progress['lessons']);
		$wCount = count($progress['watched']);
		$percentage = ($wCount / $sCount) * 100;

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
		$progress = $this->getProgress();

		return count($progress['lessons']) == count($progress['watched']);
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
		if($this->course->is_locked || !$this->course->areAllStarterSeen()) {
			return true;
		}

		if($this->is_tag_locked())
		{
			return true;
		}

		// Get previous module
		$prevModule = $this->previous_module;
		if((!$prevModule || $prevModule->is_completed) && !$this->is_date_locked) {
			return false;
		}

		return true;
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
	| Backpack model callbacks
	|--------------------------------------------------------------------------
	*/
	public function view_lessons_button() {
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

	public function admin_course_link() {
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
