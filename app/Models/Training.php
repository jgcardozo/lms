<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use App\Scopes\SessionTypeScope;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Training extends Model
{
	use CrudTrait;
	use Sluggable;
	use BackpackCrudTrait;
	use SluggableScopeHelpers;

	protected $table = 'sessions';

	protected $fillable = ['title', 'slug', 'description', 'video_url', 'video_duration', 'bucket_url', 'type', 'course_id', 'featured_image', 'learn_more', 'featured_training_coachingcall'];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(new OrderScope);
		static::addGlobalScope(new SessionTypeScope(self::class));
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

	public function resources()
	{
		return $this->belongsToMany('App\Models\Resource', 'resource_session', 'session_id');
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

	public function getRouteKeyName()
	{
		return 'slug';
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

	public function featured_marker()
	{
		if(!$this->featured_training_coachingcall) return;

		echo 'Yes';
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
}