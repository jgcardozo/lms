<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use App\Models\Course;
use App\Models\ISTag;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('vendor.backpack.base.dashboard');

    }

    protected function percentage($total,$portion)
    {
        if($total == 0) {
            return 0;
        }

        return round(($portion/$total)*100,2);
    }

    public function formInputFields()
    {
        $courses = Course::with('cohorts','modules.lessons')->get()->toArray();

        return $courses;

    }

    public function pieChartsData(Request $request)
    {
        $s = microtime(true);

        $moduleCount = [];
        $lessonCount = [];
        $sessionCount = [];
        $moduleAvgCompletion = [];
        $lessonAvgCompletion = [];

        $courses = Course::with('tags','modules.lessons.sessions')->get();


        if($request->filled('course_id')) {
            $course_id = $request->input('course_id');
            $tag_id = $courses->find($course_id)->tags->first()->id;
        }
        else {
            $course_id = $courses->first()->id;
            $tag_id = $courses->first()->tags->first()->id;
        }



        if($request->filled('cohort_id')) {
            $cohort_id = $request->input('cohort_id');
            $users = Cohort::find($cohort_id)->users()->get()->pluck('id')->toArray();
        } else {
            $users = ISTag::with('users')->find($tag_id)->users()->get()->pluck('id')->toArray();
        }

        $totalUsers = count($users);

        if($totalUsers === 0) {
            return response("error",200);
        }

        foreach ($courses->find($course_id)->modules as $module) {
            $moduleCount[$module->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Module' AND progress_id=? AND user_id IN (".implode(",",$users).")",[$module->id]);
            $moduleCount[$module->title] = $moduleCount[$module->title][0]->counted;
            $moduleCount[$module->title] = $this->percentage($totalUsers,$moduleCount[$module->title]);

            if($module->lmsLessons->count() > 0) {
                $sessionId = $module->lmsLessons->first()->sessions->first()->id;

                $query = DB::select("SELECT CEIL(AVG(t3.diff)) as avg FROM (SELECT t1.user_id,TIMESTAMPDIFF(DAY,t1.st,t2.en) as diff FROM (SELECT created_at  as st,user_id FROM `progresses` WHERE (progress_type LIKE '%Session' AND progress_id=?) AND user_id IN (".implode(",",$users).")) t1
INNER JOIN (SELECT created_at  as en,user_id FROM `progresses` WHERE (progress_type LIKE '%Module' AND progress_id=?) AND user_id IN (".implode(",",$users).")) t2 ON t1.user_id = t2.user_id) t3",[$sessionId,$module->id]);

                $moduleAvgCompletion[$module->title] = $query[0]->avg;

                if($moduleAvgCompletion[$module->title] == null) {
                    $moduleAvgCompletion[$module->title] = 0;
                }
            } else {
                $moduleAvgCompletion[$module->title] = 0;
            }

        }

        if($request->filled('module_id')) {
            foreach (Module::find($request->input('module_id'))->lmsLessons as $lesson) {

                $lessonCount[$lesson->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Lesson' AND progress_id=? AND user_id IN (".implode(",",$users).")",[$lesson->id]);
                $lessonCount[$lesson->title] = $lessonCount[$lesson->title][0]->counted;
                $lessonCount[$lesson->title] = $this->percentage($totalUsers,$lessonCount[$lesson->title]);

                $sessionId = $lesson->sessions->first()->id;

                $query = DB::select("SELECT CEIL(AVG(t3.diff)) as avg FROM (SELECT t1.user_id,TIMESTAMPDIFF(DAY,t1.st,t2.en) as diff FROM (SELECT created_at  as st,user_id FROM `progresses` WHERE (progress_type LIKE '%Session' AND progress_id=?) AND user_id IN (".implode(",",$users).")) t1
INNER JOIN (SELECT created_at  as en,user_id FROM `progresses` WHERE (progress_type LIKE '%Lesson' AND progress_id=?) AND user_id IN (".implode(",",$users).")) t2 ON t1.user_id = t2.user_id) t3",[$sessionId,$lesson->id]);

                $lessonAvgCompletion[$lesson->title] = $query[0]->avg;

                if($lessonAvgCompletion[$lesson->title] == null) {
                    $lessonAvgCompletion[$lesson->title] = 0;
                }

            }
        }

        if($request->filled('lesson_id')) {
            foreach (Lesson::find($request->input('lesson_id'))->sessions as $session) {
                $sessionCount[$session->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Session' AND progress_id=? AND user_id IN (".implode(",",$users).")",[$session->id]);
                $sessionCount[$session->title] = $sessionCount[$session->title][0]->counted;
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

        $e = microtime(true);

        return [
            $modulePieChart,
            $lessonPieChart,
            $sessionPieChart,
            $moduleAvgCompletion,
            $lessonAvgCompletion,
            $colorPallete,
            $moduleCount,
            $lessonCount,
            $sessionCount,
            $e-$s,
        ];
    }
}
