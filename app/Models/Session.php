<?php

namespace App\Models;

use Auth;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Session extends Model
{
	use CrudTrait, Sluggable, SluggableScopeHelpers, BackpackCrudTrait;

	protected $fillable = ['title', 'description', 'video_url', 'video_duration', 'starter_course_id', 'lesson_id', 'lock_date'];

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
	 * Mark this session as watched by user
	 *
	 * @param $user
	 */
	public function markAsComplete($user = null) {
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
	public function getIsCompletedAttribute($user = null) {
		if(!$user) {
			$user = Auth::user();
		}

		return $this->usersWatched()->where('user_id', $user->id)->exists();
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

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function resources() {
		return $this->belongsToMany('App\Models\Resource', 'resource_session');
	}

	public function lesson() {
		return $this->belongsTo('App\Models\Lesson');
	}

	public function course() {
		return $this->belongsTo('App\Models\Course', 'starter_course_id', 'id');
	}

	public function usersWatched() {
		return $this->belongsToMany('App\Models\User', 'session_user');
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
	public function admin_lesson_link() {
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
