<?php

namespace App\Models;

use App\Traits\RecordActivity;
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
use Illuminate\Support\Facades\DB;
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
	use RecordActivity;

	protected $fillable = [
		'title', 'slug', 'description', 'video_url', 'video_type_id', 'video_duration', 'bucket_url', 'type', 'course_id', 'featured_image', 'starter_course_id', 'lesson_id', 'lock_date', 'learn_more'
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

    public function getPreviousSessionAttribute()
    {
        $prevSession = $this->lesson->sessions->where('lft', '<', $this->lft)->last();

        return !$prevSession ? false : $prevSession;
    }

    /**
     * Check if this session is locked
     *
     * @return bool
     */
    public function getIsLockedAttribute()
    {
        if(is_role_admin()) {
            return false;
        }

        if(!$this->course->is_locked && is_role_vip())
        {
            return false;
        }

        if($this->lesson->is_locked) {
            return true;
        }

        if(!$this->isCourseMustWatch() && !$this->is_date_locked)
        {
            return false;
        }

        $prevSession = $this->previous_session;
        if((!$prevSession || $prevSession->is_completed) && !$this->is_date_locked)
        {
            return false;
        }

        return true;
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

        if(is_numeric($user))
        {
            $user = \App\Models\User::find($user);
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

	public function getHierarchyNameAttribute()
    {
        $name = '';
        if($this->lesson !== null) {
            $name = "[ ".$this->lesson->title." ] ".$name;
            if($this->lesson->module !== null) {
                $name = "[ ".$this->lesson->module->title." ] ".$name;
            }
        }

        $name = $name.$this->title;

        return $name;
    }

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
    public function schedules()
    {
        return $this->morphToMany(Schedule::class,'schedulable');
    }


    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'subject');
    }

    public function progress()
    {
        return $this->morphMany('App\Models\Progress', 'progress');
    }

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
		return $this->belongsToMany('App\Models\User', 'session_user')->withPivot(['created_at','user_id','session_id']);
	}

    public function video_type()
    {
        return $this->belongsTo('App\Models\VideoType');
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

    public function getDripOrLockDays($schedule_id)
    {
        $id = $this->id;

        $table_row = DB::table('schedulables')
            ->select('drip_days','lock_date')
            ->where([
                ['schedule_id', $schedule_id],
                ['schedulable_id', $id],
                ['schedulable_type',"App\Models\Session"]
            ])->get()->first();

        if (empty($table_row)) {
            $schedule = Schedule::find($schedule_id);
            $schedule->sessions()->attach($this);

            DB::table('schedulables')
                ->where([
                    ['schedule_id', $schedule_id],
                    ['schedulable_id', $id],
                    ['schedulable_type',"App\Models\Session"]
                ])->update([
                    'drip_days' => 0,
                ]);

            return 0;
        }

        if (!empty($table_row->lock_date)) {
            $session_days = date("m/d/Y h:i A", strtotime($table_row->lock_date));
        } else {
            $session_days = $table_row->drip_days;
        }
        return $session_days;
    }
}
