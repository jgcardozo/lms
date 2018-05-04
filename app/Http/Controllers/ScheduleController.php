<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use Illuminate\Http\Request;
use App\Models\Course;

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

        $course = Course::with('modules.lessons')->find($course_id);

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
}
