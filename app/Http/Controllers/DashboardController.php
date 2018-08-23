<?php

namespace App\Http\Controllers;

use App\Jobs\CacheCompletionData;
use App\Models\Cohort;
use App\Models\Course;
use App\Models\ISTag;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

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
        shell_exec('cd .. && php artisan db:seed --class CacheSeeder --force');
    }
}
