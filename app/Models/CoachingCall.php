<?php

namespace App\Models;

use Auth;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Scopes\SessionTypeScope;
use App\Traits\BackpackCrudTrait;
use App\Scopes\CoachingCallUserScope;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class CoachingCall extends Model
{
	use CrudTrait;
	use Sluggable;
	use BackpackCrudTrait;
	use SluggableScopeHelpers;

    protected $table = 'sessions';

	protected $fillable = ['title', 'slug', 'description', 'video_url', 'video_type_id', 'video_duration', 'bucket_url', 'type', 'course_id', 'featured_image', 'learn_more', 'featured_training_coachingcall', 'top_coachingcall'];

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
		if(!is_role_admin())
		{
			static::addGlobalScope(new CoachingCallUserScope(Auth::user()->created_at));
		}
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

	public function getRouteKeyName()
	{
		return 'slug';
	}

    /**
     * Scope a query to only include top coaching calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTop($query)
    {
        return $query->where('top_coachingcall', '=', 1);
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