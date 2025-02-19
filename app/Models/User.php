<?php

namespace App\Models;

use App\Traits\ISLock;
use Carbon\Carbon;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\InfusionsoftController;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{

    use ISLock;
    use HasRoles;
    use CrudTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'contact_id',
        'cohort_id',
        'email',
        'password',
        'activation_code',
        'timezone',
        'user_profile_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        static::created(function ($model)
        {

            $log = new \App\Models\Log;
            if (Auth::check()) {
                $log->user_id = Auth::id();
            } else {
                $log->user_id = 1;
            }

            $log->action_id = 14;
            $log->activity_id = 7;
            $log->subject_type = get_class($model);
            $log->subject_id = $model->id;
            $log->save();
        });

        static::updated(function($model) {
            $log = new \App\Models\Log;

            if (Auth::check()) {
                $log->user_id = Auth::id();
            } else {
                $log->user_id = 1;
            }
            
            $log->action_id = 8;
            $log->activity_id = 7;
            $log->subject_type = get_class($model);
            $log->subject_id = $model->id;
            $log->save();
        });

        static::deleted(function($model) {
            $log = new \App\Models\Log;
            $log->user_id = Auth::id();
            $log->action_id = 13;
            $log->activity_id = 7;
            $log->deleted_user = $model->email;
            $log->save();

            $logs = $model->logs;

            foreach ($logs as $logg) {
                $logg->deleted_user = $model->email;

                $logg->save();
            }
        });
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function hasTag($tag)
    {
        if (empty($tag))
        {
            return false;
        }

        return (bool)$this->is_tags->contains('id', $tag);
    }

    public function getTZAttribute()
    {
        if ( ! $this->timezone)
        {
            return config('app.timezone');
        }

        return $this->timezone;
    }

    /**
     * Syncs new IS tags
     *
     * @return (array) New tags attached
     */
    public function syncIsTags()
    {
        // Sync Infusionsoft user tags
        $is = new InfusionsoftController($this);
        $newTags = $is->sync();

        if ( ! empty($newTags))
        {
            Log::info('User tags updated. | ID: ' . implode(', ', $newTags));
        }

        // Check for unlocked course/module/lesson/session
        // and notify the user
        /*
        $items = $is->checkUnlockedCourses($newTags);
        if(!empty($is))
        {
            foreach($items as $item)
            {
                $event->user->notify(new UnlockedByTag($item));
            }
        }
        */

        $data = [
            'event' => 'lms_sync_tags',
            'tags' => $this->is_tags->pluck('id'),
            'user_id' => $this->id
        ];

        $curl = curl_init();

        curl_setopt_array($curl,[
            CURLOPT_URL => env('LMSV2_URL')."/hooks/chfy8356md",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ]
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

//        $curl = curl_init();
//
//        curl_setopt_array($curl,[
//            CURLOPT_URL => "https://develop.ask.academy/hooks/chfy8356md",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 30000,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS => json_encode($data),
//            CURLOPT_HTTPHEADER => [
//                // Set here requred headers
//                "accept: */*",
//                "accept-language: en-US,en;q=0.8",
//                "content-type: application/json",
//            ]
//        ]);
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);

        return $is->sync();
    }

    public function UnlockDate($lesson)
    {
        $course_id = $lesson->course->id;
        $reflection = new \ReflectionClass($lesson);
        $class_name = $reflection->getName();

        $cohort = $this->cohorts()->where('course_id',$course_id)->first();

        if (empty($cohort)) {
            $schedule = Schedule::where([
                ['status',"default"],
                ['course_id',$course_id]
            ])->first();
        } else {
            $schedule = Schedule::find($cohort->schedule_id);
        }


        $schedule_type = $schedule->schedule_type;

        if ($schedule_type === "locked") {
            $column_name = "lock_date";
        } else {
            $column_name = "drip_days";
        }


        $dateOrDay = DB::table('schedulables')
            //->select($column_name) //juanUpdate
            ->where([
                'schedule_id' => $schedule->id,
                'schedulable_type' => $class_name,
                'schedulable_id' => $lesson->id
            ])->first();
        $dripTime = $dateOrDay->drip_time; //juanUpdate        
        $dateOrDay = $dateOrDay->$column_name;


        if ($schedule_type === "locked" && Carbon::parse($dateOrDay)->gte(now())) {
            return Carbon::parse($dateOrDay)->toFormattedDateString('m/d/Y');
        }

        if ($schedule_type === "dripped") {
            $course = Course::find($course_id);
            $tag_id = $course->tags->first()->id;
            //$created = $this->is_tags()->where('id',$tag_id)->first()->pivot->created_at;
            // juan 22-nov-2023  ,  dayZero Field
            $created = (isset($schedule->day_zero) && !is_null($schedule->day_zero))
                ? Carbon::parse($schedule->day_zero)
                : $this->is_tags()->where('id', $tag_id)->first()->pivot->created_at;
            //dd("created_at",$created);  
            // END juan 22-nov-2023  ,  dayZero Field

            $unlock_day = Carbon::parse($created);
            $unlock_day->hour = 8;
            $unlock_day->minute = 0;
            $unlock_day->second = 0;
            if (isset($dripTime) && !is_null($dripTime)) { //juanUpdate
                $dripTimeCarbon = Carbon::createFromFormat('H:i:s', $dripTime);
                $unlock_day->hour = $dripTimeCarbon->hour;
                $unlock_day->minute = $dripTimeCarbon->minute;
            }
            $unlock_day->addDays($dateOrDay);


            if ($unlock_day->gte(now())) {
                return "in ".$unlock_day->diffForHumans(null,true);
            }

        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\Log::class);
    }

    public function cohorts()
    {
        return $this->belongsToMany(Cohort::class);
    }

    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    public function sessionsWatched()
    {
        return $this->belongsToMany('App\Models\Session', 'session_user');
    }

    public function is_tags()
    {
        return $this->belongsToMany('App\Models\ISTag', 'tag_user', 'user_id', 'tag_id')
                    ->withPivot('created_at');
    }

    public function fb_posted()
    {
        return $this->belongsToMany('App\Models\Lesson', 'fb_lesson', 'user_id', 'lesson_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Backpack model callbacks
    |--------------------------------------------------------------------------
    */
    public function admin_user_login_url()
    {
        return '<input type="text" readonly="readonly" onClick="this.select();" value="' . route('user.autologin', ['id'    => $this->id,
                                                                                                                    'email' => $this->email,
                                                                                                                    'key'   => \Autologin::getKey()]) . '" style="width: 100%" />';
    }

    public function view_user_activity()
    {
        ?>
        <a href="<?php echo route('log.index',['user_id' => $this->id]); ?>" class="btn btn-xs btn-default">
            <i class="fa fa-eye"></i>
            View Activity
        </a>
        <?php
    }
}
