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
        
       // dd("lockviauserDate=",$reflection->getName());

        if ($class_name === "Module") {
            $course_id = $this->course_id;
        } elseif ($class_name === "Lesson") {
            $course_id = $this->course->id;
        } elseif ($class_name === "Session") {
            //dd("llegoSession", $this, $this->id);
            $course_id = $this->course->id;
            //dd("llegoSession", $course_id);
        }
        /* elseif ($class_name === "Resource") { //juanUpdate
            dd("llegoREsource", $this, $this->id);
            //$course_id = $this->sessions->course->id;
            //dd("llegoREsource", $course_id);
        } */

        //dd($class_name,$course_id);
        /*$query = $user->cohorts()->where('course_id', $course_id);
        dd($query->toSql());*/
        $cohort = $user->cohorts()->where('course_id',$course_id)->first();
        //dd($cohort);

        if (empty($cohort)) {
            $schedule = Schedule::where([
                ['status',"default"],
                ['course_id',$course_id]
            ])->first();
            //dd("cohort empty", $schedule); //schedule 55 course 21
        } else {
            // 56 or 61 schedule depends on cohort_user
            //dd("cohort not empty","cohort->schedule_id",$cohort->schedule_id);
            if (empty($cohort->schedule_id)) {
                $schedule = Schedule::where('course_id',$cohort->course_id)->where('status','default')->get()->first();
            }
            else {
                $schedule = Schedule::find($cohort->schedule_id);
            }
        }
        //dd($cohort);
        $schedule_type = $schedule->schedule_type;


	    if ($schedule_type === "locked") {
	        $column_name = "lock_date";
        } else {
	        $column_name = "drip_days";
        }

        //dd($schedule, $class_name, $schedule_type);    //55 dripped  , 56 locked  or 66 video,learn n resource
    
        /*$query = DB::table('schedulables')
            ->select($column_name)
            ->where([
                    'schedule_id' => $schedule->id,
                    'schedulable_type' => $reflection->getName(),
                    'schedulable_id' => $this->id
                ]);
        dd($query->toSql());*/
        
     
        if ($class_name === "Resource") {
            $dateOrDay = DB::table('schedulables')
                ->where([
                    'schedule_id' => $schedule->id,
                    'schedulable_type' => "App\\Models\\".$class_name,
                    'schedulable_id' => $this->id
                ])->first();
        } else {
            $dateOrDay = DB::table('schedulables')
                //->select($column_name) //juanUpdate
                ->where([
                    'schedule_id' => $schedule->id,
                    'schedulable_type' => $reflection->getName(),
                    'schedulable_id' => $this->id
                ])->first();

        }       
 


        //dd("dateorDAy",$dateOrDay);
        if(!empty($dateOrDay)) {
            //dd("entro1",$dateOrDay);
            $dripTime = $dateOrDay->drip_time; //juanUpdate
            $dateOrDay  = $dateOrDay->$column_name;
            //dd("entro1",$dateOrDay, $dripTime);
        } else {
            //dd("entro aca");
            if ($schedule_type === "locked") {
                $dateOrDay = DB::table('schedulables')
                    ->select($column_name)
                    ->where([
                        'schedule_id' => $schedule->id,
                        'schedulable_type' => $reflection->getName()
                    ])->orderBy($column_name,'DESC')->first()->$column_name;
            } else {
                $dateOrDay = 0;
            }
        }
        //dd("before schedule_type=", $schedule_type ,$dateOrDay );//, Carbon::parse($dateOrDay));

        if ($schedule_type === "locked" && Carbon::parse($dateOrDay)->lte(now())) {
            //dd("entra aca");  
            // pej no entra y se vuelve true, en module=95 del 24oct , select * from schedulables where schedule_id =56
            return false;
        }
        //dd("schedule_type",$schedule_type);
        if ($schedule_type === "dripped") {
           
            if ($schedule->status=='default'){
                //dd("jj entro a drip sch_id=" . $schedule);
                $schedule->day_zero = "2000-01-01 08:00:00";
                $schedule->save();
            }
           
            
            $course = Course::find($course_id);
            //dd($course);
            $tag_id = $course->tags->first()->id;
            //dd($tag_id); 35836

            // juan 22-nov-2023  ,  dayZero Field
            //dd($schedule->day_zero);
            //dd(isset($schedule->day_zero) && !is_null($schedule->day_zero));
            $created = (isset($schedule->day_zero) && !is_null($schedule->day_zero))
            ? Carbon::parse($schedule->day_zero)
            : $user->is_tags()->where('id',$tag_id)->first()->pivot->created_at;
            //dd("created_at",$created); //  select * from tag_user where tag_id=35836 and user_id=214 
            // END juan 22-nov-2023  ,  dayZero Field

            $unlock_day = Carbon::parse($created);
            //dd($unlock_day, now());
            $unlock_day->hour = 0;
            $unlock_day->minute = 0;
            $unlock_day->second = 0;

            
            if (isset($dripTime) && !is_null($dripTime)){ //juanUpdate
                $dripTimeCarbon = Carbon::createFromFormat('H:i:s', $dripTime);
                $unlock_day->hour   = $dripTimeCarbon->hour;
                $unlock_day->minute = $dripTimeCarbon->minute;
            }
            //echo '-'.$unlock_day->hour.':'.$unlock_day->minute;
            //dd($unlock_day, $dateOrDay, $dripTime);
            
         
            //$unlock_day->addDays(8);
            $unlock_day->addDays($dateOrDay);
            //dd("after unlock_day->addDays", $unlock_day);


            if ($unlock_day->lte(now())) {
                return false;
            }

        }

        return true;

	}
}