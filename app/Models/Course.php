<?php

namespace App\Models;

use Auth;
use App\Models\Session;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\IgnoreCoachingCallsScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Course extends Model
{
	use CrudTrait, Sluggable, SluggableScopeHelpers, BackpackCrudTrait;

	protected $fillable = ['title', 'short_description', 'description', 'video_url', 'featured_image', 'module_group_title', 'lock_date'];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot() {
		parent::boot();

		static::addGlobalScope(new OrderScope);
		static::addGlobalScope(new IgnoreCoachingCallsScope);
	}

	/**
	 * Method that checks if all
	 * starter videos are seen
	 *
	 * @param null $user
	 * @return bool
	 */
	public function areAllStarterSeen($user = null) {
		if(!$user) {
			$user = Auth::user();
		}

		$starter_videos = $this->starter_videos->pluck('id')->toArray();
		$watched_videos = $user->sessionsWatched->pluck('id')->toArray();

		$check = array_intersect($starter_videos, $watched_videos);

		if(count($check) == count($starter_videos)) {
			return true;
		}

		return false;
	}

	/**
	 * Get all sessions that belongs to this course
	 *
	 * @return array
	 */
	public function getAllSessions() {
		$tmp = [];

		foreach($this->modules as $module) {
			foreach($module->lessons as $lesson) {
				$tmp = array_merge($tmp, $lesson->sessions->pluck('id')->toArray());
			}
		}

		return $tmp;
	}

	/**
	 * Get next session to be resumed
	 *
	 * @param null $user
	 * @return bool
	 */
	public function getNextSession($user = null) {
		if(!$user) {
			$user = Auth::user();
		}

		$courseSessions = $this->getAllSessions();
		$watched_videos = $user->sessionsWatched->pluck('id')->toArray();

		foreach($courseSessions as $session) {
			if(in_array($session, $watched_videos)) continue;

			return Session::find($session);
			break;
		}

		return false;
	}

	/**
	 * Check if course is locked
	 *
	 * @return bool
	 */
	public function getIsLockedAttribute() {
		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
    public function modules()
    {
		return $this->hasMany('App\Models\Module');
	}

	public function starter_videos()
    {
		return $this->hasMany('App\Models\Session', 'starter_course_id');
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

	public function coachingcall()
    {
		return $this->hasOne('App\Models\CoachingCall');
	}

	public function events()
    {
		return $this->hasMany('App\Events');
	}

    public function tags()
    {
        return $this->morphToMany('App\Models\ISTag', 'lockable', 'is_lockables', 'lockable_id', 'tag_id');
    }

	/*
	|--------------------------------------------------------------------------
	| Mutators
	|--------------------------------------------------------------------------
	*/
	public function setImageAttribute($value) {
		$attribute_name = 'featured_image';
		$disk = 's3';
		$destination_path = 'course_' . $this->slug . '/';

		$request = \Request::instance();
		$file = $request->file('featured_image');
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();

		// Make the image
		$image = \Image::make($file);

		// Store the image on disk
		\Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString());

		// Save the path to the database
		$this->attributes[$attribute_name] = $destination_path.'/'.$filename;
	}

	/*
	|--------------------------------------------------------------------------
	| Backpack model callbacks
	|--------------------------------------------------------------------------
	*/
	public function view_modules_button() {
		ob_start();
		?>
		<a href="<?php echo route('crud.module.index', ['course' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View modules
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}

	public function view_intros_button() {
		if(!$this->starter_videos) return;
		?>
		<a href="<?php echo route('crud.session.index', ['course' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View intros
		</a>
		<?php
	}
}
