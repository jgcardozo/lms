<?php

namespace App\Http\Controllers;

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
}
