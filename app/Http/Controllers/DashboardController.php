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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $cache = Cache::get('4-15-30');
        return view('vendor.backpack.base.dashboard', compact('cache'));

    }

    public function formInputFields()
    {
        $courses = Course::with('cohorts', 'modules.lessons')->get()->toArray();

        return $courses;

    }

    public function pieChartsData(Request $request)
    {
        $s = microtime(true);

        if ($request->filled('course_id')) {
            $course_id = $request->input('course_id');
        } else {
            $course_id = Course::first()->id;
        }

        $e = microtime(true);

        if ($request->filled('module_id')) {

            if($request->filled('lesson_id')) {

                if($request->filled('cohort_id')) {
                    return Cache::get($course_id . "-" . $request->input('module_id')."-".$request->input('lesson_id')."-c".$request->input('cohort_id'));
                }

                return Cache::get($course_id . "-" . $request->input('module_id')."-".$request->input('lesson_id'));

            }

            if($request->filled('cohort_id')) {
                return Cache::get($course_id . "-" . $request->input('module_id')."-c".$request->input('cohort_id'));
            }
            return Cache::get($course_id . "-" . $request->input('module_id'));

        }

        if($request->filled('cohort_id')) {
            return Cache::get($course_id ."-c".$request->input('cohort_id'));
        }

        return Cache::get($course_id);
    }


    public function cacheFill()
    {
        Artisan::call('db:seed',['--class' => 'CacheSeeder']);

        return 'cached';
    }




}


/*foreach ($module->lmsLessons as $lesson) {

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

    foreach ($lesson->sessions as $session) {
        $sessionCount[$session->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Session' AND progress_id=? AND user_id IN (".implode(",",$users).")",[$session->id]);
        $sessionCount[$session->title] = $sessionCount[$session->title][0]->counted;
        $sessionCount[$session->title] = $this->percentage($totalUsers,$sessionCount[$session->title]);
    }

}*/
