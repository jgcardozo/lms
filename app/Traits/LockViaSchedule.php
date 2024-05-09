<?php

namespace App\Traits;

/*
Author: Juan cardozo - ideaware
Date: 2023-10-27
Description:  this is for lock Resources Video, Learn withIn session
view: lms/courses/session-popup.blade 
*/


use App\Models\Course;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait LockViaSchedule
{


    /**
     * Check if item is locked
     * with future date
     *
     * @return bool
     */
    public function lockedAtSchedule($sessionId, $courseId, $sessionField = null)
    { //dd("paso2   LockViaSchedule-trait sessionId:$sessionId , courseId:$courseId, sessionField:$sessionField");
        $user = Auth::user();
        $reflection = new \ReflectionClass($this);
        $class_name = $reflection->getShortName(); //dd($reflection->getName());
        $course_id = $courseId;
        //dd("llegoComo=$class_name courseId=$course_id");

        $cohort = $user->cohorts()->where('course_id', $course_id)->first();

        if (empty($cohort)) {
            $schedule = Schedule::where([
                ['status', "default"],
                ['course_id', $course_id]
            ])->first();
            //dd("cohort empty", $schedule); //schedule 55 course 21
        } else {
            // 56 or 61 schedule depends on cohort_user
            //dd("cohort not empty","cohort->schedule_id",$cohort->schedule_id);
            if (empty($cohort->schedule_id)) {
                $schedule = Schedule::where('course_id', $cohort->course_id)->where('status', 'default')->get()->first();
            } else {
                $schedule = Schedule::find($cohort->schedule_id);
            }
        }

        $schedule_type = $schedule->schedule_type;

        $schedulableModel = $class_name === "Resource" ? $reflection->getName() : $reflection->getName() . $sessionField;
        //dd($user, $schedule, $class_name, $schedule_type, $cohort, $reflection->getName(), "sessionField: " . $sessionField, "schedulableModel: ". $schedulableModel);
        //55 dripped  , 56 locked  or 66 video,learn n resource
        $dateOrDay = DB::table('schedulables')
            ->where([
                'schedule_id' => $schedule->id,
                'schedulable_type' => $schedulableModel,
                'schedulable_id' => $this->id
            ])->first();

         if(is_null($dateOrDay)){
            //dd("entra aca pq es locked ó no tiene el registro de sessionVideo o learn en schedule");
            return false;
         }else{
            //dd("si tiene el registro de sessionVideo o learn en schedule");
            if ($schedule_type === "dripped") {
                $dripTime = $dateOrDay->drip_time; 
                $dateOrDay = $dateOrDay->drip_days;
                //dd("dripDays",$dateOrDay, $dripTime);
                $course = Course::find($course_id);
                $tag_id = $course->tags->first()->id;
              
                //$created = $user->is_tags()->where('id', $tag_id)->first()->pivot->created_at;
                // juan 22-nov-2023  ,  dayZero Field
                $created = (isset($schedule->day_zero) && !is_null($schedule->day_zero))
                    ? Carbon::parse($schedule->day_zero)
                    : $user->is_tags()->where('id', $tag_id)->first()->pivot->created_at;
                //dd("created_at",$created); //  select * from tag_user where tag_id=35836 and user_id=214 
                // END juan 22-nov-2023  ,  dayZero Field

                $unlock_day = Carbon::parse($created);
                $unlock_day->hour = 0;
                $unlock_day->minute = 0;
                $unlock_day->second = 0;

                if (isset($dripTime) && !is_null($dripTime)) { //juanUpdate
                    $dripTimeCarbon = Carbon::createFromFormat('H:i:s', $dripTime);
                    $unlock_day->hour = $dripTimeCarbon->hour;
                    $unlock_day->minute = $dripTimeCarbon->minute;
                }
                //echo '-' . $unlock_day->hour . ':' . $unlock_day->minute;
                //dd($unlock_day, $dateOrDay, $dripTime);

                //$unlock_day->addDays(3);  //decomment just for testing
                $unlock_day->addDays($dateOrDay);
                //dd("after unlock_day->addDays", $unlock_day);
                if ($unlock_day->lte(now())) {
                    return false;
                }
            }else if ($schedule_type === "locked"){ 
                //dd("esta en schedule, pero es locked");
                $dateOrDay = $dateOrDay->lock_date;
                if ( Carbon::parse($dateOrDay)->lte(now()) ) {
                    //dd("entra aca");  
                    // pej no entra y se vuelve true, en module=95 del 24oct , select * from schedulables where schedule_id =56
                    return false;
                }
            }
         }    
        //dd('schedule_id', $schedule->id , 'schedulable_type', $schedulableModel,"dateorDAy",$dateOrDay);
    
        return true;

    } //funct


}//trait