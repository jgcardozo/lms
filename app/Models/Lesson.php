<?php

namespace App\Models;

use Auth;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Lesson extends Model
{
	use CrudTrait, Sluggable, SluggableScopeHelpers, BackpackCrudTrait;

	protected $fillable = ['title', 'description', 'video_url', 'module_id', 'lock_date'];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot() {
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
	public function getProgress($user = null) {
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
	public function getProgressPercentage() {
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
	public function getPreviousLessonAttribute() {
		$prevLesson = $this->module->lessons->where('lft', '<', $this->lft)->first();

		return !$prevLesson ? false : $prevLesson;
	}

	/**
	 * Check if all the sessions within this lesson
	 * have been marked as watched by the user
	 *
	 * @return bool
	 */
	public function getIsCompletedAttribute() {
		$progress = $this->getProgress();

		return count($progress['sessions']) == count($progress['watched']) && count($progress['sessions']) > 0;
	}

	/**
	 * Check if the module is locked
	 * with future date
	 *
	 * @return bool
	 */
	public function getIsDateLockedAttribute() {
		if(!empty($this->lock_date)) {
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
	public function getIsLockedAttribute() {
		if($this->module->is_locked) {
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

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function module() {
		return $this->belongsTo('App\Models\Module');
	}

	public function sessions() {
		return $this->hasMany('App\Models\Session');
	}

	public function sluggable() {
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
	public function view_sessions_button() {
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

	public function admin_module_link() {
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
