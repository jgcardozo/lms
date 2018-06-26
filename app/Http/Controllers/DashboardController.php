<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('vendor.backpack.base.dashboard');

    }

    protected function percentage($total,$portion)
    {
        return round(($portion/$total)*100,2);
    }

    public function formInputFields()
    {
        $courses = Course::with('cohorts','modules.lessons')->get()->toArray();

        return $courses;

    }

    public function pieChartsData(Request $request)
    {
        $moduleCount = [];
        $lessonCount = [];
        $sessionCount = [];
        $moduleAvgCompletion = [];
        $lessonAvgCompletion = [];


        if($request->filled('course_id')) {
            $course_id = $request->input('course_id');
            $tag_id = Course::find($course_id)->tags->first()->id;
        }
        else {
            $course_id = Course::first()->id;
            $tag_id = Course::first()->tags->first()->id;
        }

        $users = User::whereHas('is_tags',function ($query) use ($tag_id){
            $query->where('id',$tag_id);
        });

        if($request->filled('cohort_id')) {
            $cohort_id = $request->input('cohort_id');
            $users->whereHas('cohorts',function ($query) use($cohort_id) {
                $query->where('cohort_id',$cohort_id);
            });
        }


        $users = $users->get();
        $totalUsers = $users->count();

        if($totalUsers === 0) {
            return response("error",200);
        }

        foreach (Course::find($course_id)->modules as $module) {
            $moduleCount[$module->title] = 0;
            $moduleAvgCompletion[$module->title] = [];


            foreach ($users as $user) {
                $moduleComplete = true;
                foreach ($module->lmsLessons as $lesson) {
                    if (!$lesson->getIsCompletedAttribute($user->id)) {
                        $moduleComplete = false;
                    }
                }
                if ($moduleComplete) {
                    $moduleCount[$module->title]++;
                    $dateEnd = Course::find($course_id)->modules->where('id',$module->id)->first()->lmsLessons->last()->sessions->last()->usersWatched()->where('user_id', $user->id)->first()->pivot->created_at;
                    $dateStart = Course::find($course_id)->modules->where('id',$module->id)->first()->lmsLessons->first()->sessions->where('starter_course_id',null)->first()->usersWatched()->where('user_id', $user->id)->first()->pivot->created_at;
                    $moduleAvgCompletion[$module->title][] = date_diff($dateEnd,$dateStart)->days;

                }
            }

            $moduleAvgCompletion[$module->title] = array_sum($moduleAvgCompletion[$module->title])/count($moduleAvgCompletion[$module->title]);

            $moduleCount[$module->title] = $this->percentage($totalUsers,$moduleCount[$module->title]);
        }

        if($request->filled('module_id')) {
            foreach (Module::find($request->input('module_id'))->lmsLessons as $lesson) {
                $lessonCount[$lesson->title] = 0;
                $lessonAvgCompletion[$lesson->title] = [];
                foreach ($users as $user) {
                    if ($lesson->getIsCompletedAttribute($user->id)) {
                        $lessonCount[$lesson->title]++;
                        $dateEnd = $lesson->sessions->last()->usersWatched()->where('user_id', $user->id)->first()->pivot->created_at;
                        $dateStart = $lesson->sessions->where('starter_course_id',null)->first()->usersWatched()->where('user_id', $user->id)->first()->pivot->created_at;
                        $lessonAvgCompletion[$lesson->title][] = date_diff($dateEnd,$dateStart)->days;
                    }
                }

                $lessonAvgCompletion[$lesson->title] = array_sum($lessonAvgCompletion[$lesson->title])/count($lessonAvgCompletion[$lesson->title]);
                $lessonCount[$lesson->title] = $this->percentage($totalUsers,$lessonCount[$lesson->title]);
            }
        }

        if($request->filled('lesson_id')) {
            foreach (Lesson::find($request->input('lesson_id'))->sessions as $session) {
                $sessionCount[$session->title] = 0;
                foreach ($users as $user) {
                    if ($session->getIsCompletedAttribute($user->id)) {
                        $sessionCount[$session->title]++;
                    }
                }

                $sessionCount[$session->title] = $this->percentage($totalUsers,$sessionCount[$session->title]);
            }
        }

        $modulePieChart = [];
        $lessonPieChart = [];
        $sessionPieChart = [];

        foreach ($moduleCount as $key => $value) {
            $modulePieChart[$key] = $this->percentage(array_sum($moduleCount),$value);
        }
        foreach ($lessonCount as $key => $value) {
            $lessonPieChart[$key] = $this->percentage(array_sum($lessonCount),$value);
        }
        foreach ($sessionCount as $key => $value) {
            $sessionPieChart[$key] = $this->percentage(array_sum($sessionCount),$value);
        }

        $colorPallete = [
            '#2a2a72',
            '#3b5249',
            '#34252f',
            '#1c3738',
            '#b68f40',
            '#531253',
            '#5e747f',
            '#aaae8e',
            '#92140c',
            '#111d4a',
            '#f28f3b',
            '#8f7e4f',
            '#191923',
            '#df2935'
        ];

        shuffle($colorPallete);

        return [
            $modulePieChart,
            $lessonPieChart,
            $sessionPieChart,
            $moduleAvgCompletion,
            $lessonAvgCompletion,
            $colorPallete
        ];
    }
}
