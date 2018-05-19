<?php

namespace App\Traits;


use App\Models\Course;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait LockViaUserDate
{
	/**
	 * Get user lock date via course model
	 *
	 * @return Carbon\Carbon
	 */
	public function getUserLockDateAttribute()
	{
		return $this->course->user_lock_date;
	}

	/**
	 * Check if item is locked
	 * with future date
	 *
	 * @return bool
	 */
	public function getIsDateLockedAttribute()
	{
	    $user = Auth::user();

	    $reflection = new \ReflectionClass($this);
	    $class_name = $reflection->getShortName();

        if ($class_name === "Module") {
            $course_id = $this->course_id;
        } elseif ($class_name === "Lesson") {
            $course_id = $this->course->id;
        } elseif ($class_name === "Session") {
            return false;
        }


        $cohort = $user->cohorts()->where('course_id',$course_id)->first();

        if (empty($cohort)) {
            $schedule = Schedule::where([
                ['status',"default"],
                ['course_id',$course_id]
            ])->first();
        } else {
            if (empty($cohort->schedule_id)) {
                $schedule = Schedule::where('course_id',$cohort->course_id)->where('status','default')->get()->first();
            }
            else {
                $schedule = Schedule::find($cohort->schedule_id);
            }
        }


        $schedule_type = $schedule->schedule_type;

	    if ($schedule_type === "locked") {
	        $column_name = "lock_date";
        } else {
	        $column_name = "drip_days";
        }

        $dateOrDay = DB::table('schedulables')
            ->select($column_name)
            ->where([
                'schedule_id' => $schedule->id,
                'schedulable_type' => $reflection->getName(),
                'schedulable_id' => $this->id
            ])->first();

        $dateOrDay = $dateOrDay->$column_name;


        if ($schedule_type === "locked" && Carbon::parse($dateOrDay)->lte(now())) {
            return false;
        }

        if ($schedule_type === "dripped") {
            $course = Course::find($course_id);
            $tag_id = $course->tags->first()->id;
            $created = $user->is_tags()->where('id',$tag_id)->first()->pivot->created_at;

            $unlock_day = Carbon::parse($created);
            $unlock_day->hour = 8;
            $unlock_day->minute = 0;
            $unlock_day->second = 0;
            $unlock_day->addDays($dateOrDay);


            if ($unlock_day->lte(now())) {
                return false;
            }

        }

        return true;

	}
}