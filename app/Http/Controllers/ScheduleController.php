<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function next(Request $request)
    {
        $request->validate([
            'course_id' => 'required | integer',
            'schedule_type' => 'required | string'
        ]);

        $course_id = $request->input('course_id');
        $schedule_type = $request->input('schedule_type');

        $course = Course::with('modules.lessons.sessions')->find($course_id);

        return $course;
    }

    public function getCohorts(Request $request)
    {
        $request->validate([
           'course_id' => 'required'
        ]);

        $course_id = $request->input('course_id');

        $cohorts = Cohort::where('course_id',$course_id)->pluck('name','id');

        return $cohorts;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'course_id' => 'required',
            'schedule_type' => 'required'
        ]);

        dd($request);
    }

    public function dripOrLock(Request $request)
    {
        $schedule_id = $request->input('schedule_id');

        $data =  DB::table('schedulables')
            ->select()
            ->where([
                ['schedule_id',$schedule_id]
            ])->get()->toArray();

        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = array_filter((array)$data[$i]);
            if(isset($data[$i]['lock_date'])) {
                $data[$i]['lock_date'] = date("m/d/Y h:i A", strtotime($data[$i]['lock_date']));
            }
        }
        return $data;
    }
}
