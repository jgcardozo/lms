<?php

namespace App\Models;

use App\Traits\RecordActivity;
use Auth;
use Carbon\Carbon;
use App\Traits\ISLock;
use App\Scopes\OrderScope;
use App\Traits\IsFreeWatch;
use Backpack\CRUD\CrudTrait;
use App\Traits\LockViaUserDate;
use App\Traits\BackpackCrudTrait;
use App\Traits\BackpackUpdateLFT;
use App\Traits\UsearableTimezone;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Activitylog\Traits\LogsActivity;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Module extends Model
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
        'title', 'slug', 'description', 'video_url', 'video_type_id', 'course_id', 'lock_date', 'featured_image', 'lesson_group_title', 'module_status'
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

        foreach ($this->lmsLessons as $lesson) {
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

        if ($sCount == 0 || $wCount == 0) {
            $percentage = 0;
        } else {
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
        foreach ($this->lmsLessons as $lesson) {
            if (!$lesson->is_completed) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if module is locked by any reason
     *
     * @return bool Returns true if is locked
     */
    public function getLockedByTagAttribute()
    {
        if (is_role_admin()) {
            return true;
        }

        if ($this->is_tag_locked()) {
            return false;
        }

        return !$this->course->is_locked;
    }

    /**
     * Check if module is locked by any reason
     *
     * @return bool Returns true if is locked
     */
    public function getIsLockedAttribute()
    {
        if (is_role_admin()) {
            return false;
        }

        // 10-abril-2024  
        // issue course purchased 7 years ago by tomkaules1+asklive@gmail.com  course_id=3 schedule_id=1
        if (!$this->course->is_locked || !$this->is_tag_locked()) {
            return false;
        }

        if (!$this->course->is_locked && is_role_vip() && !$this->is_tag_locked()) {
            return false;
        }

        if (
            $this->course->is_locked ||
            $this->course->course_canceled ||
            (!$this->course->areAllStarterSeen() && $this->isCourseMustWatch()) ||
            $this->is_tag_locked()
        ) {
            return true;
        }

        if (!$this->isCourseMustWatch() && !$this->is_date_locked) {
            return false;
        }

        // Get previous module
        $prevModule = $this->previous_module;
        if ((!$prevModule || $prevModule->is_completed) && !$this->is_date_locked) {
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

        return !empty($this->featured_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . rawurlencode($this->featured_image) : '';
    }

    

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function progress()
    {
        return $this->morphMany('App\Models\Progress', 'progress');
    }

    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'subject');
    }

    public function schedules()
    {
        return $this->morphToMany(Schedule::class, 'schedulable');
    }


    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }

    public function lessons()
    {
        return $this->hasMany('App\Models\Lesson');
    }

    public function lmsLessons()
    {
        return $this->hasMany('App\Models\Lesson')->where('exclude_from_rule', '!=', true);
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
        ?>
		<a href="<?php echo route('crud.lesson.index', ['module' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View lessons
		</a>
		<?php
    }

    public function admin_course_link()
    {
        if (!$this->course) {
            return;
        } ?>
		<a href="<?php echo route('crud.course.edit', [$this->course->id]); ?>">
			<?php echo $this->course->title ?>
		</a>
		<?php
    }

    public function view_in_frontend_button()
    {
        ?>
		<a target="_blank" href="<?php echo route('single.module', $this->slug); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View module
		</a>
		<?php
    }

    public function getDripOrLockDays($schedule_id)
    {
        $id = $this->id;

        $table_row = DB::table('schedulables')
            ->select('drip_days', 'lock_date')
            ->where([
                ['schedule_id', $schedule_id],
                ['schedulable_id', $id],
                ['schedulable_type',"App\Models\Module"]
            ])->get()->first();

        if (empty($table_row)) {
            $schedule = Schedule::find($schedule_id);
            $schedule->modules()->attach($this);

            DB::table('schedulables')
                ->where([
                    ['schedule_id', $schedule_id],
                    ['schedulable_id', $id],
                    ['schedulable_type',"App\Models\Module"]
                ])->update([
                    'drip_days' => 0,
                ]);

            return 0;
        }

        if (!empty($table_row->lock_date)) {
            $module_days = date("m/d/Y h:i A", strtotime($table_row->lock_date));
        } else {
            $module_days = $table_row->drip_days;
        }
        return $module_days;
    }
}
